# Student & Academic History — How It Works

This explains how student records and their academic progression (batch,
major, year, semester, etc.) are modeled and stored, and how every screen
that touches this data actually works under the hood.

## The core rule

**A student's current state and a student's history are two different
things, stored in two different tables.** Editing a student's batch/major/
year/semester never overwrites the past — it closes out the old record and
opens a new one. This is why a student's academic story can always be
reconstructed later, even after dozens of changes.

## The three tables

### `students`

The student's identity and *current* academic state, live. One row per
student, forever (soft-deleted, never hard-deleted except via the explicit
bulk-destroy tooling).

Key columns:

| Column | Meaning |
|---|---|
| `code` | Student ID, unique within `(major_id, status_id)` — the same code can be reused under a different major, or the same major with a different status (e.g. re-enrolling after "Dropout") |
| `batch_id`, `major_id`, `group_id`, `shift_id`, `campus_id`, `status_id` | Current placement — all foreign keys, `campus_id` is the only nullable one |
| `year_level` | Current year of study (1, 2, 3, ...) |
| `semester` | Current semester (1 or 2), **nullable** — see "Why semester is its own column" below |
| `payment_as`, `degree_type`, `admission_date`, `from_school`, `intake`, `scholarship`, `entrance_exam`, `exit_exam`, `bacc_2_code` | Other enrollment metadata |

Model: `app/Models/Student.php`

### `student_academic_histories`

One row per academic *snapshot* — a frozen copy of everything in the table
above that actually changed, plus which term it happened under. This table
only grows; existing rows are never edited except to flip `is_current` to
`false` when a newer snapshot replaces them.

| Column | Meaning |
|---|---|
| `student_id` | Which student |
| `term_id` | Which term was globally active when this snapshot was recorded (nullable — see Terms below) |
| `batch_id`, `major_id`, `group_id`, `shift_id`, `campus_id`, `status_id`, `year_level`, `semester` | The frozen values, as of `effective_date` |
| `effective_date` | When this snapshot became true |
| `is_current` | Exactly one row per student has this set to `true` — the student's present state |

Model: `app/Models/StudentAcademicHistory.php`. Relations:
`Student::academicHistories()` (all of them) and
`Student::currentAcademicHistory()` (the one with `is_current = true`).

### `terms`

Academic calendar periods (e.g. "S2-2025", year 2025, semester 2,
Apr–Jul). **Exactly one term is ever active** (`is_active = true`) at a
time, enforced by `Api\TermController` — activating one deactivates every
other one, both via the normal `store`/`update` endpoints and the
dedicated one-click `PATCH /terms/{term}/activate`.

A term's `semester` field describes the *calendar period* ("this is the
school's 2nd semester of the 2025 academic year"), not any individual
student's progress — see the next section for why that distinction
matters.

Model: `app/Models/Term.php`. Managed at `/term` (sidebar → Term).

## Why `semester` is its own column, not derived from Term

The obvious shortcut — "a student's semester is whatever the active
term's semester is" — doesn't hold up: only **one** term can be active
system-wide, but two students can legitimately be on different semesters
of their own program at the same calendar moment (a retake, a deferral,
a transfer). Tying semester to the global active term would make that
impossible to represent.

So `semester` lives directly on `students` and
`student_academic_histories`, independent of Term. Term still gets
stamped onto every snapshot (useful for "show me everyone active in
Fall 2026"-style reporting), but it no longer determines anyone's
semester.

## How a change actually gets recorded (`Api\StudentController`)

Three different actions can create a new `student_academic_histories`
row. All three funnel through the same private method,
`advanceAcademicHistory()`:

```
if (nothing in [batch_id, major_id, group_id, shift_id, campus_id,
                status_id, year_level, semester] changed)
    and (not explicitly advancing, OR the active term hasn't moved on)
  → do nothing

otherwise:
  → flip the current history row to is_current = false
  → insert a new history row with the student's current values + the
    now-active term_id
```

1. **`store()`** — creating a student always creates its first history
   row (`is_current = true`, no prior row to close).
2. **`update()`** — the full edit-student form. Only creates a new
   history row if one of the academic fields *actually changed*. Fixing
   a typo in `from_school` six months from now must never silently
   "advance" someone just because the active term moved on in the
   meantime — so this path does **not** treat a term change alone as a
   reason to advance.
3. **`advanceSemester()`** / **`bulkAdvanceSemester()`** — the dedicated
   "Advance Semester" actions (single-student modal and bulk modal on
   the Student page). These pass `forTermChangeToo: true`, so even when
   *none* of the student's own fields change (e.g. Year 1 Semester 1 →
   Year 1 Semester 2 — same batch, same major, same year), the fact that
   the active term is now different is enough on its own to record a new
   snapshot.

### Bulk advance specifics

`bulkAdvanceSemester()` (route: `PATCH /api/v1/students-bulk-advance-semester`)
takes either an explicit list of student `ids`, or `{ all: true, filters:
{...} }` to apply to everyone matching a filter (major/batch/shift/group/
campus/status). The `changes` payload only ever contains fields the
registrar actually set in the modal — anything left as "No change" is
never touched, so bulk-advancing a filtered major to a new semester
doesn't force everyone into an identical batch.

Safety rule: the filter-scoped (`all: true`) form is rejected with a 422
unless **both** `filters.batch_id` and `filters.campus_id` are present —
enforced both client-side (the button stays disabled) and server-side (so
a direct API call can't skip the UI's guardrail). An explicit `ids` list
doesn't need this, since each id there was already hand-picked.

## Where to see this data

- **Student list** (`/student`) — filter by major/batch/shift/group/
  campus/status, select rows via checkbox (or "select all matching
  filters"), and Advance Semester in bulk from the toolbar that appears.
- **Advance Semester** (teal calendar icon on a student row) — the
  single-student version, pre-filled with their current values.
- **Student History** (`/student-history`, sidebar → Student History) —
  search a student, see their full timeline: every snapshot, newest
  first, current one marked, each showing which term it was recorded
  under.
- **Term** (`/term`, sidebar → Term) — create terms, see which one is
  active, one-click activate another.

## Import/export and the Semester backfill problem

`StudentExport`/`StudentImport` round-trip the same spreadsheet columns
(export the list, edit, re-import). Semester has no reliable value for
students imported before this column existed — there's nothing to derive
it from. Rather than guess, the import fills it in **only when it's
currently blank**:

- A brand-new row (no matching `code` + `major_id` + `status_id`) creates
  the student with whatever Semester value the row provides (or `null`).
- A row matching an **existing** student is normally skipped entirely
  (this importer never bulk-updates existing students). The one
  exception: if the row provides a Semester value and that student's
  `semester` is still `null`, it fills in *only* that field — nothing
  else on the existing student is touched. Already-set values are never
  overwritten, so exporting → filling in known values → re-importing is
  safe to repeat as many times as needed.

## Permissions

Everything here rides on the existing `student.*` and `term.*` permission
modules (`database/seeders/PermissionSeeder.php`) — `student.view` for
the list, history page, and read-only endpoints; `student.edit` for
advancing (single and bulk); `term.*` for the Term management page. No
separate permission module was introduced for any of this.
