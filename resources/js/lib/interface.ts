interface IConfig {
    endpoint: string;
    slug?: string;
    defaultSort?: string;
    defaultDir?: "asc" | "desc";
    perPage?: number;
}

interface IRow {
    id: number | string;
    [key: string]: unknown;
}

interface IPage {
    data: IRow[];
    meta: {
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
}

interface IState {
    items: IRow[];
    loading: boolean;
    search: string;
    sortField: string;
    sortDir: "asc" | "desc";
    page: number;
    perPage: number;
    totalPages: number;
    total: number;
    item: IRow | null;
    _searchTimer: ReturnType<typeof setTimeout> | null;

    init(): void;
    fetchData(): Promise<void>;
    sortBy(field: string): void;
    confirmDelete(row: IRow): void;
    deleteRow(): Promise<void>;

    // Alpine magics available on `this` inside x-data
    $el: HTMLElement;
    $watch: (prop: string, cb: (value: unknown) => void) => void;
}

export { IConfig, IPage, IRow, IState };
