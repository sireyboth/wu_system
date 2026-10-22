import { baseUri } from "../app";

export const CONFIG = {
    TRANSCRIPTS_API: baseUri('transcripts'),
    DEBOUNCE_DELAY: 300,
    PER_PAGE: 15,
    // Must match TranscriptRequest::MAX_PER_SEMESTER / MAX_YEAR.
    MAX_PER_SEMESTER: 5,
    YEARS: 4,
    ROMANS: { 1: 'I', 2: 'II', 3: 'III', 4: 'IV' },
};
