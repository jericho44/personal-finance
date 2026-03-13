import { ICategory } from './category';

export interface IBudget {
    id: string;
    idHash: string;
    categoryId: number;
    amount: number;
    startDate: string;
    endDate: string;
    isActive: boolean;
    notes?: string | null;
    category?: ICategory | null;
    createdAt?: string;
    updatedAt?: string;
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
    isOverBudget: boolean;
}
