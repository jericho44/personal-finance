import { IDataTable } from "./global";

export interface IUser {
    id: string,
    name: string,
    username: string,
    role: IRole
    isActive: boolean

}

export interface IRole {
    id: string,
    name: string,
    slug?: string,
}

export interface IDataTableUser extends IDataTable {
    data: IUser[];
}

export interface IUserDetail extends IUser {
    createdAt: string
}

export interface ICreateUserPayload {
    name: string,
    username: string,
    password: string,
    roleId: string,

}
export interface IEditUserPayload {
    name: string,
    username: string,
    roleId: string,
}
