export interface IBill {
    id: string;
    idHash: string;
    name: string;
    amount: number;
    dueDate: string;
    frequency: 'once' | 'daily' | 'weekly' | 'monthly' | 'yearly';
    isPaid: boolean;
    categoryId?: number | null;
    notes?: string | null;
    category?: any | null; // using any for category shorthand, or import ICategory
    createdAt?: string;
    updatedAt?: string;
}

export interface IBillPayload {
    name: string;
    amount: number;
    due_date: string;
    frequency: 'once' | 'daily' | 'weekly' | 'monthly' | 'yearly';
    is_paid: boolean;
    category_id?: number | null;
    notes?: string | null;
}

export interface IBillFilters {
    category_id?: number | null;
    is_paid?: boolean | string;
}
