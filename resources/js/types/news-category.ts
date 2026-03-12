import { IDataTable } from "./global";

export interface INewsCategory {
    id: string,
    name: string,    
    isActive:boolean

}

export interface IDataTableNewsCategory extends IDataTable {
    data: INewsCategory[];
}

export interface INewsCategoryDetail extends INewsCategory {
    createdAt: string
}

export interface ICreateNewsCategoryPayload {
    name: string,

}
export interface IEditNewsCategoryPayload {
    name: string,

}
