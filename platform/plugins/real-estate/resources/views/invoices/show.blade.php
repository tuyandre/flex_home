@extends('core/base::layouts.master')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="invoice-info">
                <div class="mb-5">
                    <div class="row align-items-center">
                        <div class="col-md-9 row">
                            <div class="col-lg-3">
                                <strong class="text-brand">{{ trans('plugins/real-estate::invoice.code') }}:</strong>
                                #{{ $invoice->code }}
                            </div>
                            <div class="col-lg-3">
                                <strong class="text-brand">{{ trans('plugins/real-estate::invoice.status') }}:</strong>
                                {!! $invoice->status->toHtml() !!}
                            </div>
                            <div class="col-lg-3">
                                <strong class="text-brand">{{ trans('plugins/real-estate::invoice.purchase_at') }}:</strong>
                                {{ $invoice->created_at->translatedFormat('j F, Y') }}
                            </div>
                            <div class="col-lg-3">
                                <strong class="text-brand">{{ trans('plugins/real-estate::invoice.payment_method') }}:</strong>
                                {{ $invoice->payment->payment_channel->label() }}
                            </div>
                        </div>
                        <div class="col-md-3 text-end">
                            <h2 class="uppercase">{{ trans('plugins/real-estate::invoice.heading') }}</h2>
                            <ul class="mt-3">
                                <li><strong>{{ $invoice->account->company }}</strong></li>
                                <li>{{ $invoice->account->name }}</li>
                                <li>{{ $invoice->account->email }}</li>
                                <li>{{ $invoice->account->phone }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <table class="table table-striped mb-3">
                    <thead>
                    <tr>
                        <th>{{ trans('plugins/real-estate::invoice.item.name') }}</th>
                        <th>{{ trans('plugins/real-estate::invoice.item.qty') }}</th>
                        <th class="text-center">{{ trans('plugins/real-estate::invoice.amount') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($invoice->items as $item)
                        <tr>
                            <td>
                                <p class="mb-0">{{ $item->name }}</p>
                                @if ($item->description)
                                    <small>{{ $item->description }}</small>
                                @endif
                            </td>
                            <td>{{ number_format($item->qty) }}</td>
                            <td class="text-center">{{ format_price($item->amount) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <th colspan="2" class="text-end">{{ trans('plugins/real-estate::invoice.sub_total') }}:</th>
                        <th class="text-center">{{ format_price($invoice->sub_total) }}</th>
                    </tr>
                    @if($invoice->tax_amount > 0)
                        <tr>
                            <th colspan="2" class="text-end">{{ trans('plugins/real-estate::invoice.detail.tax_amount') }}:</th>
                            <th class="text-center">{{ format_price($invoice->tax_amount) }}</th>
                        </tr>
                    @endif
                    @if($invoice->discount_amount > 0)
                        <tr>
                            <th colspan="2" class="text-end">{{ trans('plugins/real-estate::invoice.discount_amount') }}:</th>
                            <th class="text-center">{{ format_price($invoice->discount_amount) }}</th>
                        </tr>
                    @endif
                    <tr>
                        <th colspan="2" class="text-end">{{ trans('plugins/real-estate::invoice.total_amount') }}:
                        </th>
                        <th class="text-center">{{ format_price($invoice->amount) }}</th>
                    </tr>
                    </tfoot>
                </table>
                <div class="row">
                    <div class="col-md-12 text-end">
                        <h5>{{ trans('plugins/real-estate::invoice.total_amount') }}</h5>
                        <h3 class="mt-0 mb-0 text-danger">{{ format_price($invoice->amount) }}</h3>
                    </div>
                </div>
                <hr>
            </div>
        </div>
        <div class="card-footer text-center">
            <a href="{{ route('invoices.generate', ['id' => $invoice->id, 'type' => 'print']) }}" target="_blank" class="btn btn-danger">
                <i class="fas fa-print"></i> {{ trans('plugins/real-estate::invoice.print') }}
            </a>
            <a href="{{ route('invoices.generate', ['id' => $invoice->id, 'type' => 'download']) }}" target="_blank" class="btn btn-success">
                <i class="fas fa-download"></i> {{ trans('plugins/real-estate::invoice.download') }}
            </a>
        </div>
    </div>
@endsection
