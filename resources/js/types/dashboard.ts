import { ITransaction } from './transaction';

export interface IDashboardData {
    net_worth: number;
    current_month: {
        income: number;
        expense: number;
        balance: number;
    };
    spending_by_category: Array<{
        category: string;
        color: string;
        total: number;
    }>;
    recent_transactions: ITransaction[];
    budgets: {
        total_limit: number;
        total_spent: number;
        percentage: number;
    };
    bills: {
        upcoming_count: number;
    };
    goals: {
        total_count: number;
        completed_count: number;
        overall_progress: number;
    };
}

export interface IDashboardResponse {
    success: boolean;
    data: IDashboardData;
}
