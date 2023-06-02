<?php

namespace Botble\RealEstate\Http\Controllers\Fronts;

use App\Http\Controllers\Controller;
use Botble\Base\Facades\Assets;
use Botble\RealEstate\Models\Invoice;
use Botble\RealEstate\Repositories\Interfaces\InvoiceInterface;
use Botble\RealEstate\Supports\InvoiceHelper;
use Botble\RealEstate\Tables\AccountInvoiceTable;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;
use Botble\Optimize\Facades\OptimizerHelper;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Botble\Base\Facades\PageTitle;

class InvoiceController extends Controller
{
    public function __construct(Repository $config, protected InvoiceInterface $invoiceRepository)
    {
        Assets::setConfig($config->get('plugins.real-estate.assets'));

        OptimizerHelper::disable();
    }

    public function index(AccountInvoiceTable $accountInvoiceTable)
    {
        PageTitle::setTitle(__('Invoices'));

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add(__('My Profile'), route('public.account.dashboard'))
            ->add(__('Manage Invoices'));

        SeoHelper::setTitle(__('Invoices'));

        return $accountInvoiceTable->render('plugins/real-estate::account.table.base');
    }

    public function show(int|string $id)
    {
        $invoice = $this->invoiceRepository->findOrFail($id);

        if (! $this->canViewInvoice($invoice)) {
            abort(404);
        }

        $title = __('Invoice detail :code', ['code' => $invoice->code]);

        PageTitle::setTitle($title);

        SeoHelper::setTitle($title);

        return view('plugins/real-estate::account.dashboard.invoices.show', compact('invoice'));
    }

    public function generate(int|string $id, Request $request, InvoiceHelper $invoiceHelper)
    {
        $invoice = $this->invoiceRepository->findOrFail($id);

        if (! $this->canViewInvoice($invoice)) {
            abort(404);
        }

        if ($request->input('type') === 'print') {
            return $invoiceHelper->streamInvoice($invoice);
        }

        return $invoiceHelper->downloadInvoice($invoice);
    }

    protected function canViewInvoice(Invoice $invoice): bool
    {
        return auth('account')->id() == $invoice->payment->customer_id;
    }
}
