@component('mail::message', ['locale' => $locale])
    <p>Hello {{ $user->first_name }},</p>
    <p>
        Thank you for subscribing to one of our plans!<br />
        You can now make use of the full power of your subscription.<br /><br />
		Below you can see an overview of your booked subscription. If there is anything wrong with your subscription, feel free to contact us any time.<br /><br />
		Your new subscription:<br />
		Name: {{ $product->name }}<br />
		Quantity: {{ $subscription->quantity }}<br />
		Price (per unit): {{ sprintf('%.2f', ($price->unit_amount_decimal / 100)) . ' €'}}<br />
		Total price: {{ sprintf('%.2f', ($price->unit_amount_decimal * $subscription->quantity / 100)) . ' €'}}<br />
		Start of subscription: {{ date("d.m.Y", $subscription->start_date) }}<br />
		End of current cycle: {{ date("d.m.Y", $subscription->current_period_end) }}<br />
		Automatic renewel: {{ $subscription->cancel_at_period_end ? 'no' : 'yes' }}<br /><br />
		You can find more details about this and your other subscriptions on the subscriptions page of your webpanel via the following link:
    </p>
    @component('mail::button', ['url' => config('app.webpanel_url') ])
        My Subscriptions
    @endcomponent
    @component('mail::paragraph')
        If that doesn't work, you can also just copy the following URL into your browser:
    @endcomponent
    <a href="{{ config('app.webpanel_url') }}">{{ config('app.webpanel_url') }}</a><br /><br />
    <p>
        Error-free Greetings,
        <br />
        your {{ config('app.projectname') }} team
    </p>
@endcomponent
