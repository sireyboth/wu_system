import { baseUri } from "../app";

export const CONFIG = {
    TRANSCRIPTS_API: baseUri('transcripts'),
    SUBJECTS_API: baseUri('subjects'),
    DEBOUNCE_DELAY: 300,
    PER_PAGE: 15,
    // Must match TranscriptRequest::MAX_PER_SEMESTER / MAX_YEAR.
    MAX_PER_SEMESTER: 5,
    YEARS: 4,
    ROMANS: { 1: 'I', 2: 'II', 3: 'III', 4: 'IV' },
    // Must match App\Helpers\Degree::years().
    DEGREE_YEARS: { associate: 2, bachelor: 4, master: 2, phd: 3 },
};
