import { ICategory } from './category';

export interface IBudget {
    id_hash: string;
    category_id: number;
    amount: number;
    start_date: string;
    end_date: string;
    is_active: boolean;
    notes?: string | null;
    category?: ICategory | null;
    created_at?: string;
    updated_at?: string;
}

export interface IBudgetPayload {
    category_id: number;
    amount: number;
    start_date: string;
    end_date: string;
    is_active: boolean;
    notes?: string | null;
}

export interface IBudgetProgress {
    budget: IBudget;
    spent: number;
    remaining: number;
    percentage: number;
    is_over_budget: boolean;
}
