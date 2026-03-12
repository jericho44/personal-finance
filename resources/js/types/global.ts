/* eslint-disable @typescript-eslint/no-explicit-any */
export interface ISelectOption {
    id?: string;
    text?: string;
    html?: string;
    disabled?: boolean,
    [key: string]: any; // Dynamic key optional
}


interface IDataTableColumn {
    text: string,
    sortColumn: boolean,
    sortBy?: string,
    class?: string,
}

export interface IDataTable {
    column: IDataTableColumn[],
    data: any[],
    showPerPage: number,
    search: string,
    order: string,
    sortBy: string,
    totalData: number,
    currentPage: number,
    loading: boolean,
    showSearch: boolean,
}
