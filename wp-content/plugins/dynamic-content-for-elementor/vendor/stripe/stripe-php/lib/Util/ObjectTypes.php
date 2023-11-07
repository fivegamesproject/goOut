<?php

namespace DynamicOOOS\Stripe\Util;

class ObjectTypes
{
    /**
     * @var array Mapping from object types to resource classes
     */
    const mapping = [
        \DynamicOOOS\Stripe\Collection::OBJECT_NAME => \DynamicOOOS\Stripe\Collection::class,
        \DynamicOOOS\Stripe\Issuing\CardDetails::OBJECT_NAME => \DynamicOOOS\Stripe\Issuing\CardDetails::class,
        \DynamicOOOS\Stripe\SearchResult::OBJECT_NAME => \DynamicOOOS\Stripe\SearchResult::class,
        \DynamicOOOS\Stripe\File::OBJECT_NAME_ALT => \DynamicOOOS\Stripe\File::class,
        // The beginning of the section generated from our OpenAPI spec
        \DynamicOOOS\Stripe\Account::OBJECT_NAME => \DynamicOOOS\Stripe\Account::class,
        \DynamicOOOS\Stripe\AccountLink::OBJECT_NAME => \DynamicOOOS\Stripe\AccountLink::class,
        \DynamicOOOS\Stripe\AccountSession::OBJECT_NAME => \DynamicOOOS\Stripe\AccountSession::class,
        \DynamicOOOS\Stripe\ApplePayDomain::OBJECT_NAME => \DynamicOOOS\Stripe\ApplePayDomain::class,
        \DynamicOOOS\Stripe\ApplicationFee::OBJECT_NAME => \DynamicOOOS\Stripe\ApplicationFee::class,
        \DynamicOOOS\Stripe\ApplicationFeeRefund::OBJECT_NAME => \DynamicOOOS\Stripe\ApplicationFeeRefund::class,
        \DynamicOOOS\Stripe\Apps\Secret::OBJECT_NAME => \DynamicOOOS\Stripe\Apps\Secret::class,
        \DynamicOOOS\Stripe\Balance::OBJECT_NAME => \DynamicOOOS\Stripe\Balance::class,
        \DynamicOOOS\Stripe\BalanceTransaction::OBJECT_NAME => \DynamicOOOS\Stripe\BalanceTransaction::class,
        \DynamicOOOS\Stripe\BankAccount::OBJECT_NAME => \DynamicOOOS\Stripe\BankAccount::class,
        \DynamicOOOS\Stripe\BillingPortal\Configuration::OBJECT_NAME => \DynamicOOOS\Stripe\BillingPortal\Configuration::class,
        \DynamicOOOS\Stripe\BillingPortal\Session::OBJECT_NAME => \DynamicOOOS\Stripe\BillingPortal\Session::class,
        \DynamicOOOS\Stripe\Capability::OBJECT_NAME => \DynamicOOOS\Stripe\Capability::class,
        \DynamicOOOS\Stripe\Card::OBJECT_NAME => \DynamicOOOS\Stripe\Card::class,
        \DynamicOOOS\Stripe\CashBalance::OBJECT_NAME => \DynamicOOOS\Stripe\CashBalance::class,
        \DynamicOOOS\Stripe\Charge::OBJECT_NAME => \DynamicOOOS\Stripe\Charge::class,
        \DynamicOOOS\Stripe\Checkout\Session::OBJECT_NAME => \DynamicOOOS\Stripe\Checkout\Session::class,
        \DynamicOOOS\Stripe\CountrySpec::OBJECT_NAME => \DynamicOOOS\Stripe\CountrySpec::class,
        \DynamicOOOS\Stripe\Coupon::OBJECT_NAME => \DynamicOOOS\Stripe\Coupon::class,
        \DynamicOOOS\Stripe\CreditNote::OBJECT_NAME => \DynamicOOOS\Stripe\CreditNote::class,
        \DynamicOOOS\Stripe\CreditNoteLineItem::OBJECT_NAME => \DynamicOOOS\Stripe\CreditNoteLineItem::class,
        \DynamicOOOS\Stripe\Customer::OBJECT_NAME => \DynamicOOOS\Stripe\Customer::class,
        \DynamicOOOS\Stripe\CustomerBalanceTransaction::OBJECT_NAME => \DynamicOOOS\Stripe\CustomerBalanceTransaction::class,
        \DynamicOOOS\Stripe\CustomerCashBalanceTransaction::OBJECT_NAME => \DynamicOOOS\Stripe\CustomerCashBalanceTransaction::class,
        \DynamicOOOS\Stripe\Discount::OBJECT_NAME => \DynamicOOOS\Stripe\Discount::class,
        \DynamicOOOS\Stripe\Dispute::OBJECT_NAME => \DynamicOOOS\Stripe\Dispute::class,
        \DynamicOOOS\Stripe\EphemeralKey::OBJECT_NAME => \DynamicOOOS\Stripe\EphemeralKey::class,
        \DynamicOOOS\Stripe\Event::OBJECT_NAME => \DynamicOOOS\Stripe\Event::class,
        \DynamicOOOS\Stripe\ExchangeRate::OBJECT_NAME => \DynamicOOOS\Stripe\ExchangeRate::class,
        \DynamicOOOS\Stripe\File::OBJECT_NAME => \DynamicOOOS\Stripe\File::class,
        \DynamicOOOS\Stripe\FileLink::OBJECT_NAME => \DynamicOOOS\Stripe\FileLink::class,
        \DynamicOOOS\Stripe\FinancialConnections\Account::OBJECT_NAME => \DynamicOOOS\Stripe\FinancialConnections\Account::class,
        \DynamicOOOS\Stripe\FinancialConnections\AccountOwner::OBJECT_NAME => \DynamicOOOS\Stripe\FinancialConnections\AccountOwner::class,
        \DynamicOOOS\Stripe\FinancialConnections\AccountOwnership::OBJECT_NAME => \DynamicOOOS\Stripe\FinancialConnections\AccountOwnership::class,
        \DynamicOOOS\Stripe\FinancialConnections\Session::OBJECT_NAME => \DynamicOOOS\Stripe\FinancialConnections\Session::class,
        \DynamicOOOS\Stripe\FundingInstructions::OBJECT_NAME => \DynamicOOOS\Stripe\FundingInstructions::class,
        \DynamicOOOS\Stripe\Identity\VerificationReport::OBJECT_NAME => \DynamicOOOS\Stripe\Identity\VerificationReport::class,
        \DynamicOOOS\Stripe\Identity\VerificationSession::OBJECT_NAME => \DynamicOOOS\Stripe\Identity\VerificationSession::class,
        \DynamicOOOS\Stripe\Invoice::OBJECT_NAME => \DynamicOOOS\Stripe\Invoice::class,
        \DynamicOOOS\Stripe\InvoiceItem::OBJECT_NAME => \DynamicOOOS\Stripe\InvoiceItem::class,
        \DynamicOOOS\Stripe\InvoiceLineItem::OBJECT_NAME => \DynamicOOOS\Stripe\InvoiceLineItem::class,
        \DynamicOOOS\Stripe\Issuing\Authorization::OBJECT_NAME => \DynamicOOOS\Stripe\Issuing\Authorization::class,
        \DynamicOOOS\Stripe\Issuing\Card::OBJECT_NAME => \DynamicOOOS\Stripe\Issuing\Card::class,
        \DynamicOOOS\Stripe\Issuing\Cardholder::OBJECT_NAME => \DynamicOOOS\Stripe\Issuing\Cardholder::class,
        \DynamicOOOS\Stripe\Issuing\Dispute::OBJECT_NAME => \DynamicOOOS\Stripe\Issuing\Dispute::class,
        \DynamicOOOS\Stripe\Issuing\Transaction::OBJECT_NAME => \DynamicOOOS\Stripe\Issuing\Transaction::class,
        \DynamicOOOS\Stripe\LineItem::OBJECT_NAME => \DynamicOOOS\Stripe\LineItem::class,
        \DynamicOOOS\Stripe\LoginLink::OBJECT_NAME => \DynamicOOOS\Stripe\LoginLink::class,
        \DynamicOOOS\Stripe\Mandate::OBJECT_NAME => \DynamicOOOS\Stripe\Mandate::class,
        \DynamicOOOS\Stripe\PaymentIntent::OBJECT_NAME => \DynamicOOOS\Stripe\PaymentIntent::class,
        \DynamicOOOS\Stripe\PaymentLink::OBJECT_NAME => \DynamicOOOS\Stripe\PaymentLink::class,
        \DynamicOOOS\Stripe\PaymentMethod::OBJECT_NAME => \DynamicOOOS\Stripe\PaymentMethod::class,
        \DynamicOOOS\Stripe\PaymentMethodConfiguration::OBJECT_NAME => \DynamicOOOS\Stripe\PaymentMethodConfiguration::class,
        \DynamicOOOS\Stripe\PaymentMethodDomain::OBJECT_NAME => \DynamicOOOS\Stripe\PaymentMethodDomain::class,
        \DynamicOOOS\Stripe\Payout::OBJECT_NAME => \DynamicOOOS\Stripe\Payout::class,
        \DynamicOOOS\Stripe\Person::OBJECT_NAME => \DynamicOOOS\Stripe\Person::class,
        \DynamicOOOS\Stripe\Plan::OBJECT_NAME => \DynamicOOOS\Stripe\Plan::class,
        \DynamicOOOS\Stripe\Price::OBJECT_NAME => \DynamicOOOS\Stripe\Price::class,
        \DynamicOOOS\Stripe\Product::OBJECT_NAME => \DynamicOOOS\Stripe\Product::class,
        \DynamicOOOS\Stripe\PromotionCode::OBJECT_NAME => \DynamicOOOS\Stripe\PromotionCode::class,
        \DynamicOOOS\Stripe\Quote::OBJECT_NAME => \DynamicOOOS\Stripe\Quote::class,
        \DynamicOOOS\Stripe\Radar\EarlyFraudWarning::OBJECT_NAME => \DynamicOOOS\Stripe\Radar\EarlyFraudWarning::class,
        \DynamicOOOS\Stripe\Radar\ValueList::OBJECT_NAME => \DynamicOOOS\Stripe\Radar\ValueList::class,
        \DynamicOOOS\Stripe\Radar\ValueListItem::OBJECT_NAME => \DynamicOOOS\Stripe\Radar\ValueListItem::class,
        \DynamicOOOS\Stripe\Refund::OBJECT_NAME => \DynamicOOOS\Stripe\Refund::class,
        \DynamicOOOS\Stripe\Reporting\ReportRun::OBJECT_NAME => \DynamicOOOS\Stripe\Reporting\ReportRun::class,
        \DynamicOOOS\Stripe\Reporting\ReportType::OBJECT_NAME => \DynamicOOOS\Stripe\Reporting\ReportType::class,
        \DynamicOOOS\Stripe\Review::OBJECT_NAME => \DynamicOOOS\Stripe\Review::class,
        \DynamicOOOS\Stripe\SetupAttempt::OBJECT_NAME => \DynamicOOOS\Stripe\SetupAttempt::class,
        \DynamicOOOS\Stripe\SetupIntent::OBJECT_NAME => \DynamicOOOS\Stripe\SetupIntent::class,
        \DynamicOOOS\Stripe\ShippingRate::OBJECT_NAME => \DynamicOOOS\Stripe\ShippingRate::class,
        \DynamicOOOS\Stripe\Sigma\ScheduledQueryRun::OBJECT_NAME => \DynamicOOOS\Stripe\Sigma\ScheduledQueryRun::class,
        \DynamicOOOS\Stripe\Source::OBJECT_NAME => \DynamicOOOS\Stripe\Source::class,
        \DynamicOOOS\Stripe\SourceTransaction::OBJECT_NAME => \DynamicOOOS\Stripe\SourceTransaction::class,
        \DynamicOOOS\Stripe\Subscription::OBJECT_NAME => \DynamicOOOS\Stripe\Subscription::class,
        \DynamicOOOS\Stripe\SubscriptionItem::OBJECT_NAME => \DynamicOOOS\Stripe\SubscriptionItem::class,
        \DynamicOOOS\Stripe\SubscriptionSchedule::OBJECT_NAME => \DynamicOOOS\Stripe\SubscriptionSchedule::class,
        \DynamicOOOS\Stripe\Tax\Calculation::OBJECT_NAME => \DynamicOOOS\Stripe\Tax\Calculation::class,
        \DynamicOOOS\Stripe\Tax\CalculationLineItem::OBJECT_NAME => \DynamicOOOS\Stripe\Tax\CalculationLineItem::class,
        \DynamicOOOS\Stripe\Tax\Settings::OBJECT_NAME => \DynamicOOOS\Stripe\Tax\Settings::class,
        \DynamicOOOS\Stripe\Tax\Transaction::OBJECT_NAME => \DynamicOOOS\Stripe\Tax\Transaction::class,
        \DynamicOOOS\Stripe\Tax\TransactionLineItem::OBJECT_NAME => \DynamicOOOS\Stripe\Tax\TransactionLineItem::class,
        \DynamicOOOS\Stripe\TaxCode::OBJECT_NAME => \DynamicOOOS\Stripe\TaxCode::class,
        \DynamicOOOS\Stripe\TaxId::OBJECT_NAME => \DynamicOOOS\Stripe\TaxId::class,
        \DynamicOOOS\Stripe\TaxRate::OBJECT_NAME => \DynamicOOOS\Stripe\TaxRate::class,
        \DynamicOOOS\Stripe\Terminal\Configuration::OBJECT_NAME => \DynamicOOOS\Stripe\Terminal\Configuration::class,
        \DynamicOOOS\Stripe\Terminal\ConnectionToken::OBJECT_NAME => \DynamicOOOS\Stripe\Terminal\ConnectionToken::class,
        \DynamicOOOS\Stripe\Terminal\Location::OBJECT_NAME => \DynamicOOOS\Stripe\Terminal\Location::class,
        \DynamicOOOS\Stripe\Terminal\Reader::OBJECT_NAME => \DynamicOOOS\Stripe\Terminal\Reader::class,
        \DynamicOOOS\Stripe\TestHelpers\TestClock::OBJECT_NAME => \DynamicOOOS\Stripe\TestHelpers\TestClock::class,
        \DynamicOOOS\Stripe\Token::OBJECT_NAME => \DynamicOOOS\Stripe\Token::class,
        \DynamicOOOS\Stripe\Topup::OBJECT_NAME => \DynamicOOOS\Stripe\Topup::class,
        \DynamicOOOS\Stripe\Transfer::OBJECT_NAME => \DynamicOOOS\Stripe\Transfer::class,
        \DynamicOOOS\Stripe\TransferReversal::OBJECT_NAME => \DynamicOOOS\Stripe\TransferReversal::class,
        \DynamicOOOS\Stripe\Treasury\CreditReversal::OBJECT_NAME => \DynamicOOOS\Stripe\Treasury\CreditReversal::class,
        \DynamicOOOS\Stripe\Treasury\DebitReversal::OBJECT_NAME => \DynamicOOOS\Stripe\Treasury\DebitReversal::class,
        \DynamicOOOS\Stripe\Treasury\FinancialAccount::OBJECT_NAME => \DynamicOOOS\Stripe\Treasury\FinancialAccount::class,
        \DynamicOOOS\Stripe\Treasury\FinancialAccountFeatures::OBJECT_NAME => \DynamicOOOS\Stripe\Treasury\FinancialAccountFeatures::class,
        \DynamicOOOS\Stripe\Treasury\InboundTransfer::OBJECT_NAME => \DynamicOOOS\Stripe\Treasury\InboundTransfer::class,
        \DynamicOOOS\Stripe\Treasury\OutboundPayment::OBJECT_NAME => \DynamicOOOS\Stripe\Treasury\OutboundPayment::class,
        \DynamicOOOS\Stripe\Treasury\OutboundTransfer::OBJECT_NAME => \DynamicOOOS\Stripe\Treasury\OutboundTransfer::class,
        \DynamicOOOS\Stripe\Treasury\ReceivedCredit::OBJECT_NAME => \DynamicOOOS\Stripe\Treasury\ReceivedCredit::class,
        \DynamicOOOS\Stripe\Treasury\ReceivedDebit::OBJECT_NAME => \DynamicOOOS\Stripe\Treasury\ReceivedDebit::class,
        \DynamicOOOS\Stripe\Treasury\Transaction::OBJECT_NAME => \DynamicOOOS\Stripe\Treasury\Transaction::class,
        \DynamicOOOS\Stripe\Treasury\TransactionEntry::OBJECT_NAME => \DynamicOOOS\Stripe\Treasury\TransactionEntry::class,
        \DynamicOOOS\Stripe\UsageRecord::OBJECT_NAME => \DynamicOOOS\Stripe\UsageRecord::class,
        \DynamicOOOS\Stripe\UsageRecordSummary::OBJECT_NAME => \DynamicOOOS\Stripe\UsageRecordSummary::class,
        \DynamicOOOS\Stripe\WebhookEndpoint::OBJECT_NAME => \DynamicOOOS\Stripe\WebhookEndpoint::class,
    ];
}
