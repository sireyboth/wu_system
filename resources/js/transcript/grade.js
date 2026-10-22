/**
 * Live-preview copy of App\Helpers\Grade — same thresholds as the
 * registrar's Excel formulas. The server recomputes on save; keep both in sync.
 */
const SCALE = [
    [96, 4.0, 'A+'],
    [90, 3.75, 'A'],
    [85, 3.5, 'A-'],
    [80, 3.25, 'B+'],
    [75, 3.0, 'B'],
    [70, 2.75, 'B-'],
    [65, 2.5, 'C+'],
    [60, 2.25, 'C'],
    [55, 2.0, 'C-'],
    [50, 1.75, 'D'],
];

/** { point, letter } for a score, or null when there's no score (IP). */
export function gradeOf(score) {
    if (score === null || score === '' || Number.isNaN(Number(score))) return null;
    const value = Number(score);
    for (const [min, point, letter] of SCALE) {
        if (value >= min) return { point, letter };
    }
    return { point: 0, letter: 'F' };
}

/** Σ(grade point × credit) ÷ total credits, rounded to 2 decimals in integer hundredths. */
export function gpaOf(rows) {
    let weighted = 0;
    let credits = 0;
    rows.forEach(([point, credit]) => {
        weighted += Math.round(point * 100) * credit;
        credits += credit;
    });
    if (!credits) return 0;
    return Math.floor((2 * weighted + credits) / (2 * credits)) / 100;
}
