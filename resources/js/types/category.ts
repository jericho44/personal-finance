export interface ICategory {
    id_hash: string;
    name: string;
    type: 'income' | 'expense';
    color: string | null;
    icon: string | null;
    created_at?: string;
    updated_at?: string;
}

export interface ICategoryPayload {
    name: string;
    type: 'income' | 'expense';
    color?: string | null;
    icon?: string | null;
}
