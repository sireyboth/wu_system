<?php

use App\Http\Controllers\Api\ActivityLogController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\AlertController;
use App\Http\Controllers\Api\BatchController;
use App\Http\Controllers\Api\CampusController;
use App\Http\Controllers\Api\CertificateController;
use App\Http\Controllers\Api\AttendanceReviewController;
use App\Http\Controllers\Api\AttendanceScanController;
use App\Http\Controllers\Api\ClassScheduleController;
use App\Http\Controllers\Api\ClassScoreController;
use App\Http\Controllers\Api\ClassSectionController;
use App\Http\Controllers\Api\ClassSessionController;
use App\Http\Controllers\Api\CourseEnrollmentController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ExamStateController;
use App\Http\Controllers\Api\ExamTypeController;
use App\Http\Controllers\Api\FacultyController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\LecturerController;
use App\Http\Controllers\Api\LecturerPortalController;
use App\Http\Controllers\Api\MajorController;
use App\Http\Controllers\Api\PaymentBatchController;
use App\Http\Controllers\Api\PaymentEntryController;
use App\Http\Controllers\Api\RetakeBatchController;
use App\Http\Controllers\Api\RetakeExamPublicController;
use App\Http\Controllers\Api\RetakeRegistrationController;
use App\Http\Controllers\Api\RetakeTermController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\ShiftController;
use App\Http\Controllers\Api\StatusController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\StudentLeaveController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\TeacherAssignmentController;
use App\Http\Controllers\Api\TermController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/**
 * Route Api for application
 */
// These two are admin-only actions and must be registered — and matched —
// before the public exam-states resource below, or its {exam_state}
// wildcard show route would swallow "/report" and "/bulk" as an id first.
Route::prefix('v1')->middleware('auth')->group(function () {
    Route::prefix('exam-states')->name('exam-states.')->group(function () {
        Route::get('/report', [ExamStateController::class, 'report'])->name('report');
        Route::delete('/bulk', [ExamStateController::class, 'bulkDestroy'])->name('bulk-destroy');
        Route::get('/export', [ExamStateController::class, 'exportList'])->name('export');
        Route::post('/import', [ExamStateController::class, 'importFile'])->name('import');
    });
});

// Exam-states, exam-terms and exam-categories all stay fully public and
// unauthenticated, same reasoning — the on-site attendance/invigilator
// pages (routes/web.php's public state-exam.* group) have no login, and
// need to read (exam-terms: which are active, their time slots;
// exam-categories: their labels) and, on the admin side, write, without
// a session. The admin pages themselves stay gated at the web-route
// level (`can:state-exam.view`) — this is the same trust model the
// original exam-states endpoint already used.
Route::prefix('v1')->group(function () {
    api_routes([
        'exam-states'      => ExamStateController::class,
        'exam-terms'       => \App\Http\Controllers\Api\ExamTermController::class,
        'exam-categories'  => \App\Http\Controllers\Api\ExamCategoryController::class,
    ]);
});

// Public self-service retake-exam registration — same reasoning as
// exam-states above. There's no login/session here: every action
// re-verifies ownership with student code + date of birth rather than
// trusting a token, so this can live fully outside the 'auth' group. The
// page itself is served from routes/web.php's retake-exam.index.
Route::prefix('v1/retake-exam')->name('retake-exam-public.')->group(function () {
    Route::post('/lookup', [RetakeExamPublicController::class, 'lookup'])->name('lookup');
    Route::post('/select', [RetakeExamPublicController::class, 'select'])->name('select');
    Route::post('/confirm', [RetakeExamPublicController::class, 'confirm'])->name('confirm');
});

// Public attendance scan — same reasoning as retake-exam above: no
// student login exists, so the rotating token + student code are the
// only guardrails. Throttle key is per-IP, and a whole classroom (or
// campus Wi-Fi) commonly shares one public IP behind NAT — a real 30-
// student class scanning within the same minute is normal traffic here,
// not abuse, so this needs to be generous rather than "API default"
// tight. The scan page itself is served from routes/web.php's attend.index.
Route::prefix('v1/attend')->name('attend-public.')->middleware('throttle:120,1')->group(function () {
    Route::post('/scan', [AttendanceScanController::class, 'scan'])->name('scan');
});

Route::prefix('v1')->middleware('auth')->group(function () {
    // No permission gate — matches the /dashboard page itself, which is
    // visible to any authenticated user regardless of role.
    Route::get('/dashboard/report', [DashboardController::class, 'report'])->name('dashboard.report');

    // Custom API routes for specific controllers
    Route::prefix('certificates')->name('certificates.')->group(function () {
        Route::get('/preview-number', [CertificateController::class, 'preview'])->name('preview');
        Route::get('/report', [CertificateController::class, 'report'])->name('report');
    });

    // Retake Exam module — rebuilt on the normalized schema (retake_terms,
    // exam_types, retake_batches, retake_registrations, payment_batches,
    // payment_entries, scores, deletion_log). See the design doc in the WU
    // System project. retake-terms/exam-types/retake-batches/
    // payment-batches/payment-entries get standard CRUD via api_routes()
    // below; retake-registrations is hand-routed since most of what REG/
    // SA/Score/ACC do to a registration isn't a generic "update".
    Route::middleware('permission:retake-registration.view')->prefix('retake-registrations')->name('retake-registrations.')->group(function () {
        Route::get('/', [RetakeRegistrationController::class, 'index'])->name('index');
        Route::get('/{retake_registration}', [RetakeRegistrationController::class, 'show'])->name('show');
    });

    // REG's "prepare schedule" export — flat sibling route (like
    // customer-service below), not nested under /retake-registrations/...,
    // so it never collides with the {retake_registration} wildcard above.
    Route::middleware('permission:retake-registration.view')
        ->get('/retake-registrations-export', [RetakeRegistrationController::class, 'exportList'])
        ->name('retake-registrations.export');

    // REG's report page — same flat-sibling reasoning as export above.
    Route::middleware('permission:retake-registration.view')
        ->get('/retake-registrations-report', [RetakeRegistrationController::class, 'report'])
        ->name('retake-registrations.report');

    // Customer Service: read-only, registered students only — its own
    // permission, separate from REG's full retake-registration.view.
    Route::middleware('permission:retake-cs.view')
        ->get('/retake-registrations-customer-service', [RetakeRegistrationController::class, 'customerService'])
        ->name('retake-registrations.customer-service');

    Route::middleware('permission:retake-registration.create')
        ->post('/retake-registrations', [RetakeRegistrationController::class, 'store'])
        ->name('retake-registrations.store');

    // Split into three permissions, not one shared "edit" — the design
    // doc's RBAC notes are explicit that SA shouldn't be able to touch
    // scores and Score shouldn't be able to touch payments, so each
    // role's action lives behind its own permission rather than a coarse
    // retake-registration.edit that everyone acting on a registration
    // would need.
    Route::middleware('permission:retake-payment.edit')->prefix('retake-registrations')->name('retake-registrations.')->group(function () {
        Route::patch('/bulk-mark-paid', [RetakeRegistrationController::class, 'bulkMarkPaid'])->name('bulk-mark-paid');
        Route::patch('/{retake_registration}/mark-paid', [RetakeRegistrationController::class, 'markPaid'])->name('mark-paid');
        Route::patch('/{retake_registration}/invite-telegram', [RetakeRegistrationController::class, 'inviteTelegram'])->name('invite-telegram');
    });

    Route::middleware('permission:retake-score.edit')
        ->patch('/retake-registrations/{retake_registration}/score', [RetakeRegistrationController::class, 'setScore'])
        ->name('retake-registrations.score');

    Route::middleware('permission:retake-registration.edit')->prefix('retake-registrations')->name('retake-registrations.')->group(function () {
        Route::patch('/{retake_registration}/outcome', [RetakeRegistrationController::class, 'setOutcome'])->name('outcome');
        // Manual override, post-lock (decision #15) — REG only for now;
        // revisit if SA turns out to need this too at the front desk.
        Route::patch('/{retake_registration}/selection', [RetakeRegistrationController::class, 'updateSelection'])->name('selection');
        Route::patch('/{retake_registration}/restore', [RetakeRegistrationController::class, 'restore'])->withTrashed()->name('restore');
    });

    Route::middleware('permission:retake-registration.delete')->prefix('retake-registrations')->name('retake-registrations.')->group(function () {
        Route::delete('/{retake_registration}', [RetakeRegistrationController::class, 'destroy'])->name('destroy');
        Route::delete('/{retake_registration}/clear', [RetakeRegistrationController::class, 'force_destroy'])->name('remove');
    });

    // REG imports the 1st Supplementary URM export — creates its own new
    // batch, so this is a "create", not an "edit" on an existing one.
    Route::middleware('permission:retake-batch.create')
        ->post('/retake-batches/import', [RetakeBatchController::class, 'importFile'])
        ->name('retake-batches.import');

    // REG's batch actions: close a stage, then generate the next one from
    // it, or attach a Telegram group.
    Route::middleware('permission:retake-batch.edit')->prefix('retake-batches')->name('retake-batches.')->group(function () {
        Route::patch('/{retake_batch}/close', [RetakeBatchController::class, 'close'])->name('close');
        Route::post('/{retake_batch}/carry-forward', [RetakeBatchController::class, 'carryForward'])->name('carry-forward');
        Route::patch('/{retake_batch}/telegram', [RetakeBatchController::class, 'telegram'])->name('telegram');
    });

    Route::middleware('permission:alert.view')->prefix('alerts')->name('alerts.')->group(function () {
        Route::get('/dashboard', [AlertController::class, 'dashboard'])->name('dashboard');
        Route::get('/{alert}/logs', [AlertController::class, 'logs'])->name('logs');
    });
    Route::middleware('permission:alert.edit')->prefix('alerts')->name('alerts.')->group(function () {
        Route::post('/{alert}/complete', [AlertController::class, 'complete'])->name('complete');
        Route::post('/{alert}/snooze', [AlertController::class, 'snooze'])->name('snooze');
    });
    Route::middleware('permission:alert.delete')->delete('/alerts/bulk', [AlertController::class, 'bulkDestroy'])->name('alerts.bulk-destroy');

    Route::middleware('permission:role.view')->prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('/permission-catalog', [RoleController::class, 'permissionCatalog'])->name('permission-catalog');
    });
    Route::middleware('permission:role.create')->post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::middleware('permission:role.edit')->put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::middleware('permission:role.delete')->delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

    Route::middleware('permission:role.edit')->put('/users/{user}/roles', [UserController::class, 'updateRoles'])->name('users.roles.update');
    Route::middleware('permission:role.edit')->put('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

    Route::middleware('permission:activity.view')->prefix('activity-log')->name('activity-log.')->group(function () {
        Route::get('/', [ActivityLogController::class, 'index'])->name('index');
        Route::get('/modules', [ActivityLogController::class, 'modules'])->name('modules');
        Route::get('/users', [ActivityLogController::class, 'users'])->name('users');
    });

    // Flat sibling routes for students — registered before api_routes()'s
    // /students/{student} wildcard below so "export"/"import" are never
    // swallowed as an id (same reasoning as retake-registrations-export).
    Route::middleware('permission:student.view')
        ->get('/students-export', [StudentController::class, 'exportList'])
        ->name('students.export');
    Route::middleware('permission:student.create')
        ->post('/students-import', [StudentController::class, 'importFile'])
        ->name('students.import');
    Route::middleware('permission:student.delete')
        ->delete('/students-bulk-destroy', [StudentController::class, 'bulkDestroy'])
        ->name('students.bulk-destroy');

    // Dedicated "advance to a new semester" action — a focused form (just
    // the academic fields) instead of the full edit-student form, but it
    // goes through the exact same StudentController::advanceAcademicHistory
    // logic as a normal edit, so history stays consistent either way.
    Route::middleware('permission:student.edit')
        ->patch('/students/{student}/advance-semester', [StudentController::class, 'advanceSemester'])
        ->name('students.advance-semester');

    // Bulk version — filter the list down to a major/batch/etc (or pass
    // explicit ids) and advance every matching student in one request.
    Route::middleware('permission:student.edit')
        ->patch('/students-bulk-advance-semester', [StudentController::class, 'bulkAdvanceSemester'])
        ->name('students.bulk-advance-semester');

    // Full academic timeline for one student — read-only, same permission
    // as viewing the student themselves.
    Route::middleware('permission:student.view')
        ->get('/students/{student}/academic-history', [StudentController::class, 'academicHistory'])
        ->name('students.academic-history');

    // Flat sibling routes for subjects — same reasoning as students above:
    // registered before api_routes()'s /subjects/{subject} wildcard so
    // "export"/"import" are never swallowed as an id.
    Route::middleware('permission:subject.view')
        ->get('/subjects-export', [SubjectController::class, 'exportList'])
        ->name('subjects.export');
    Route::middleware('permission:subject.create')
        ->post('/subjects-import', [SubjectController::class, 'importFile'])
        ->name('subjects.import');
    Route::middleware('permission:subject.delete')
        ->delete('/subjects-bulk-destroy', [SubjectController::class, 'bulkDestroy'])
        ->name('subjects.bulk-destroy');

    // Flat sibling routes for lecturers — same reasoning as subjects above:
    // registered before api_routes()'s /lecturers/{lecturer} wildcard so
    // "export"/"import" are never swallowed as an id.
    Route::middleware('permission:lecturer.view')
        ->get('/lecturers-export', [LecturerController::class, 'exportList'])
        ->name('lecturers.export');
    Route::middleware('permission:lecturer.create')
        ->post('/lecturers-import', [LecturerController::class, 'importFile'])
        ->name('lecturers.import');
    Route::middleware('permission:lecturer.delete')
        ->delete('/lecturers-bulk-destroy', [LecturerController::class, 'bulkDestroy'])
        ->name('lecturers.bulk-destroy');

    // Flat sibling routes for faculties/majors/shifts/groups — same
    // reasoning as students/subjects/lecturers above: registered before
    // api_routes()'s wildcard routes below so "export"/"import" are never
    // swallowed as an id.
    Route::middleware('permission:faculty.view')
        ->get('/faculties-export', [FacultyController::class, 'exportList'])
        ->name('faculties.export');
    Route::middleware('permission:faculty.create')
        ->post('/faculties-import', [FacultyController::class, 'importFile'])
        ->name('faculties.import');
    Route::middleware('permission:faculty.delete')
        ->delete('/faculties-bulk-destroy', [FacultyController::class, 'bulkDestroy'])
        ->name('faculties.bulk-destroy');

    Route::middleware('permission:major.view')
        ->get('/majors-export', [MajorController::class, 'exportList'])
        ->name('majors.export');
    Route::middleware('permission:major.create')
        ->post('/majors-import', [MajorController::class, 'importFile'])
        ->name('majors.import');
    Route::middleware('permission:major.delete')
        ->delete('/majors-bulk-destroy', [MajorController::class, 'bulkDestroy'])
        ->name('majors.bulk-destroy');

    Route::middleware('permission:shift.view')
        ->get('/shifts-export', [ShiftController::class, 'exportList'])
        ->name('shifts.export');
    Route::middleware('permission:shift.create')
        ->post('/shifts-import', [ShiftController::class, 'importFile'])
        ->name('shifts.import');
    Route::middleware('permission:shift.delete')
        ->delete('/shifts-bulk-destroy', [ShiftController::class, 'bulkDestroy'])
        ->name('shifts.bulk-destroy');

    Route::middleware('permission:group.view')
        ->get('/groups-export', [GroupController::class, 'exportList'])
        ->name('groups.export');
    Route::middleware('permission:group.create')
        ->post('/groups-import', [GroupController::class, 'importFile'])
        ->name('groups.import');
    Route::middleware('permission:group.delete')
        ->delete('/groups-bulk-destroy', [GroupController::class, 'bulkDestroy'])
        ->name('groups.bulk-destroy');

    // Status maps to the "app-status" permission module (not "status" —
    // see PermissionSeeder::MODULES), so these use that name.
    Route::middleware('permission:app-status.view')
        ->get('/statuses-export', [StatusController::class, 'exportList'])
        ->name('statuses.export');
    Route::middleware('permission:app-status.create')
        ->post('/statuses-import', [StatusController::class, 'importFile'])
        ->name('statuses.import');
    Route::middleware('permission:app-status.delete')
        ->delete('/statuses-bulk-destroy', [StatusController::class, 'bulkDestroy'])
        ->name('statuses.bulk-destroy');

    // Register API resource routes for various controllers
    api_routes([
        'faculties'       => FacultyController::class,
        'majors'          => MajorController::class,
        'shifts'          => ShiftController::class,
        'campuses'        => CampusController::class,
        'lecturers'       => LecturerController::class,
        'subjects'        => SubjectController::class,
        'batches'         => BatchController::class,
        'groups'          => GroupController::class,
        'students'        => StudentController::class,
        'statuses'        => StatusController::class,
        'certificates'    => CertificateController::class,
        'alerts'          => AlertController::class,
        'retake-terms'    => RetakeTermController::class,
        'exam-types'      => ExamTypeController::class,
        'retake-batches'  => RetakeBatchController::class,
        'payment-batches' => PaymentBatchController::class,
        'payment-entries' => PaymentEntryController::class,
        'terms'           => TermController::class,
        'users'           => UserController::class,
        'rooms'               => RoomController::class,
        'classes'             => ClassSectionController::class,
        'class-schedules'     => ClassScheduleController::class,
        'teacher-assignments' => TeacherAssignmentController::class,
        'course-enrollments'  => CourseEnrollmentController::class,
        'student-leaves'      => StudentLeaveController::class,
        'class-scores'        => ClassScoreController::class,
    ], [
        'faculties'       => 'faculty',
        'majors'          => 'major',
        'shifts'          => 'shift',
        'campuses'        => 'campus',
        'lecturers'       => 'lecturer',
        'subjects'        => 'subject',
        'batches'         => 'batch',
        'groups'          => 'group',
        'students'        => 'student',
        'statuses'        => 'app-status',
        'certificates'    => 'certificate',
        'alerts'          => 'alert',
        'retake-terms'    => 'retake-term',
        'exam-types'      => 'exam-type',
        'retake-batches'  => 'retake-batch',
        'payment-batches' => 'payment-batch',
        'payment-entries' => 'payment-entry',
        // 'exam-states' registered separately above, fully public.
        'terms'           => 'term',
        'users'           => 'role',
        'rooms'               => 'room',
        'classes'             => 'class',
        'class-schedules'     => 'class',
        'teacher-assignments' => 'class',
        'course-enrollments'  => 'class',
        'student-leaves'      => 'student-leave',
        'class-scores'        => 'class',
    ]);

    // Class scoring — one config row per class, so this is a get-or-empty
    // + upsert, not a generic CRUD resource with its own id. See
    // ClassSectionController::scoreConfig()/updateScoreConfig().
    Route::middleware('permission:class.view')
        ->get('/classes/{class}/score-config', [ClassSectionController::class, 'scoreConfig'])
        ->name('classes.score-config.show');
    Route::middleware('permission:class.edit')
        ->put('/classes/{class}/score-config', [ClassSectionController::class, 'updateScoreConfig'])
        ->name('classes.score-config.update');

    // Automatic mixed-major rostering — enroll every student matching a
    // filter set (major deliberately optional) into this class in one
    // call, instead of adding students to a class one at a time.
    Route::middleware('permission:class.edit')
        ->post('/classes/{class}/auto-enroll', [ClassSectionController::class, 'autoEnroll'])
        ->name('classes.auto-enroll');

    // Manual add of specific students — the complement to auto-enroll,
    // for the "just this handful, maybe from another batch" case
    // (retake, add-subject, borrowed for one elective).
    Route::middleware('permission:class.view')
        ->get('/students-search-for-class', [ClassSectionController::class, 'searchStudents'])
        ->name('classes.students-search');
    Route::middleware('permission:class.edit')
        ->post('/classes/{class}/add-student', [ClassSectionController::class, 'addStudent'])
        ->name('classes.add-student');

    // Read-only attendance-history grid — every session date the class has
    // held, crossed with every enrolled student's status that day. Lets a
    // registrar (or the lecturer, via the portal route below) spot a week
    // that was never taken, not just each student's running total.
    Route::middleware('permission:class.view')
        ->get('/classes/{class}/attendance-history', [ClassSectionController::class, 'attendanceHistory'])
        ->name('classes.attendance-history');
    Route::middleware('permission:class.view')
        ->get('/classes/{class}/attendance-history/export', [ClassSectionController::class, 'exportAttendanceHistory'])
        ->name('classes.attendance-history.export');

    // Registrar approve/reject on a student leave request.
    Route::middleware('permission:student-leave.edit')
        ->patch('/student-leaves/{student_leave}/decide', [StudentLeaveController::class, 'decide'])
        ->name('student-leaves.decide');

    // Lecturer's own portal — row-scoped by TeacherAssignment inside the
    // controller, not by which classes exist overall (that's the
    // registrar's `class.*` permission, a different thing entirely).
    Route::prefix('lecturer-portal')->name('lecturer-portal.')->group(function () {
        Route::middleware('permission:lecturer-portal.view')->group(function () {
            Route::get('/classes', [LecturerPortalController::class, 'classes'])->name('classes');
            Route::get('/classes/{class}/score-config', [LecturerPortalController::class, 'scoreConfig'])->name('score-config.show');
            Route::get('/classes/{class}/roster', [LecturerPortalController::class, 'roster'])->name('roster');
            Route::get('/classes/{class}/attendance-history', [LecturerPortalController::class, 'attendanceHistory'])->name('attendance-history');
            Route::get('/classes/{class}/attendance-history/export', [LecturerPortalController::class, 'exportAttendanceHistory'])->name('attendance-history.export');
        });
        Route::middleware('permission:lecturer-portal.edit')->group(function () {
            Route::put('/classes/{class}/score-config', [LecturerPortalController::class, 'updateScoreConfig'])->name('score-config.update');
            Route::post('/scores', [LecturerPortalController::class, 'storeScore'])->name('scores.store');
            Route::post('/classes/{class}/attendance-history/import', [LecturerPortalController::class, 'importAttendanceHistory'])->name('attendance-history.import');

            // Attendance: start a session, watch it live, rotate the QR,
            // manually mark stragglers, and lock it at the end.
            Route::post('/classes/{class}/sessions', [ClassSessionController::class, 'start'])->name('sessions.start');
            Route::get('/sessions/{session}', [ClassSessionController::class, 'show'])->name('sessions.show');
            Route::get('/sessions/{session}/qr-token', [ClassSessionController::class, 'qrToken'])->name('sessions.qr-token');
            Route::post('/sessions/{session}/mark', [ClassSessionController::class, 'markManual'])->name('sessions.mark');
            Route::patch('/sessions/{session}/submit', [ClassSessionController::class, 'submit'])->name('sessions.submit');
            Route::post('/attendance-records/{record}/corrections', [ClassSessionController::class, 'requestCorrection'])->name('records.corrections.store');
        });
    });

    // Registrar action: create/link a login account for a lecturer.
    Route::middleware('permission:lecturer.edit')
        ->post('/lecturers/{lecturer}/create-account', [LecturerController::class, 'createAccount'])
        ->name('lecturers.create-account');

    // Registrar's attendance review queue — flagged scans + pending
    // corrections. Nothing here changes a record except decideCorrection's
    // approve path; everything else is read-only or a no-op acknowledgement.
    // Named "attendance-review-api.*", distinct from the web page route
    // "attendance-review.index" — both are registered under the literal
    // path /attendance-review (this one prefixed with /api/v1), and
    // sharing a route *name* between them silently made route()
    // resolve to whichever was registered last, sending the sidebar
    // link straight to raw JSON instead of the page.
    Route::prefix('attendance-review')->name('attendance-review-api.')->group(function () {
        Route::middleware('permission:attendance-review.view')
            ->get('/', [AttendanceReviewController::class, 'index'])->name('index');
        Route::middleware('permission:attendance-review.edit')->group(function () {
            Route::patch('/verifications/{verification}/review', [AttendanceReviewController::class, 'reviewVerification'])->name('verifications.review');
            Route::patch('/corrections/{correction}/decide', [AttendanceReviewController::class, 'decideCorrection'])->name('corrections.decide');
        });
    });

    // "Activate"/"Deactivate" a term — separate from the generic update()
    // so this is a one-click action in the terms list, not a full edit-form
    // submission just to flip a checkbox. Multiple terms can be active at
    // once (see Term::resolveDefault()), so neither of these touches any
    // other term's flag.
    Route::middleware('permission:term.edit')->group(function () {
        Route::patch('/terms/{term}/activate', [TermController::class, 'activate'])->name('terms.activate');
        Route::patch('/terms/{term}/deactivate', [TermController::class, 'deactivate'])->name('terms.deactivate');
    });

    // Address API routes
    Route::get('/provinces', [AddressController::class, 'provinces'])->name('provinces.all');
    Route::get('/nationalities', [AddressController::class, 'nationalities'])->name('nationalities.all');

    // Using Model Binding
    Route::get('/districts/{province}', [AddressController::class, 'districts'])->name('districts.by-province');
    Route::get('/communes/{district}', [AddressController::class, 'communes'])->name('communes.by-district');
    Route::get('/villages/{commune}', [AddressController::class, 'villages'])->name('villages.by-commune');
});
