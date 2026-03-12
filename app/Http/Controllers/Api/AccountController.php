<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    private $accountRepository;

    public function __construct(\App\Interfaces\AccountInterface $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    public function index(Request $request)
    {
        $accounts = $this->accountRepository->getAll($request->user()->id);
        return response()->json(['success' => true, 'data' => $accounts]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'type' => 'required|in:cash,bank,ewallet,credit_card,investment',
            'balance' => 'numeric',
            'currency' => 'string|max:3',
            'color' => 'nullable|string',
            'icon' => 'nullable|string',
        ]);

        $account = $this->accountRepository->create($request->all(), $request->user()->id);
        return response()->json(['success' => true, 'data' => $account]);
    }

    public function show(Request $request, $id)
    {
        $account = $this->accountRepository->findByIdHash($id, [], $request->user()->id);
        return response()->json(['success' => true, 'data' => $account]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'type' => 'required|in:cash,bank,ewallet,credit_card,investment',
            'balance' => 'numeric',
            'currency' => 'string|max:3',
            'color' => 'nullable|string',
            'icon' => 'nullable|string',
        ]);

        $account = $this->accountRepository->update($id, $request->all(), $request->user()->id);
        return response()->json(['success' => true, 'data' => $account]);
    }

    public function destroy(Request $request, $id)
    {
        $this->accountRepository->delete($id, $request->user()->id);
        return response()->json(['success' => true, 'message' => 'Account deleted']);
    }
}
