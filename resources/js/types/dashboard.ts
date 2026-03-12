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
}

export interface IDashboardResponse {
    success: boolean;
    data: IDashboardData;
}
