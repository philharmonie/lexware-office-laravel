<?php

namespace PHPSTORM_META {

    override(\PhilHarmonie\LexOffice\Facades\Contact::class, map([
        '' => '@|\PhilHarmonie\LexOffice\Services\ContactService',
    ]));

    override(\PhilHarmonie\LexOffice\Facades\Invoice::class, map([
        '' => '@|\PhilHarmonie\LexOffice\Services\InvoiceService',
    ]));

    override(\PhilHarmonie\LexOffice\Facades\Dunning::class, map([
        '' => '@|\PhilHarmonie\LexOffice\Services\DunningService',
    ]));

    override(\app(0), map([
        'PhilHarmonie\LexOffice\Contracts\ClientInterface' => \PhilHarmonie\LexOffice\Client::class,
        'PhilHarmonie\LexOffice\Services\ContactService' => \PhilHarmonie\LexOffice\Services\ContactService::class,
        'PhilHarmonie\LexOffice\Services\InvoiceService' => \PhilHarmonie\LexOffice\Services\InvoiceService::class,
        'PhilHarmonie\LexOffice\Services\DunningService' => \PhilHarmonie\LexOffice\Services\DunningService::class,
    ]));
}
