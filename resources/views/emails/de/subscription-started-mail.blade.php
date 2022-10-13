@component('mail::message', ['locale' => $locale])
	<p>Hallo {{ $user->first_name }},</p>
	<p>
		vielen Dank, dass Du einen unserer Pläne abonniert hast!<br />
		Du kannst jetzt die volle Leistung Deines Abonnements nutzen.<br /><br />
		Folgend findest Du eine kurze Zusammenfassung deines gebuchten Abonnements. Sollte etwas mit Deinem Abonnement nicht in Ordnung sein, kannst Du uns jederzeit kontaktieren.<br /><br />
		Dein neues Abonnement:<br />
		@foreach($products as $product)
		Name: {{ $product->parent_product->name }}<br />
		Menge: {{ $product->quantity }}<br />
		Preis (pro Einheit): {{ sprintf('%.2f', ($product->plan->amount / 100)) . ' €'}}<br />
		Gesamtpreis: {{ sprintf('%.2f', ($product->plan->amount * $product->quantity / 100)) . ' €'}}<br /><br />
		@endforeach
		Gesamtpreis (Abonnement): {{ sprintf('%.2f', ($totalSubscriptionPrice / 100)) . ' €'}}<br />
		Beginn des Abonnements: {{ date("d.m.Y", $subscription->start_date) }}<br />
		Ende des aktuellen Zyklus: {{ date("d.m.Y", $subscription->current_period_end) }}<br />
		Automatische Erneuerung: {{ $subscription->cancel_at_period_end ? 'Nein' : 'Ja' }}<br /><br />
		Weitere Informationen zu diesem und Deinen anderen Abonnements findest Du unter dem Punkt "Meine Abos" in Deinem Webpanel unter folgendem Link:
	</p>
	@component('mail::button', ['url' => config('app.webpanel_url') ])
		Meine Abos
	@endcomponent
    @component('mail::paragraph')
        Falls das nicht klappt, kannst Du auch einfach die folgende URL in deinen Browser kopieren:
    @endcomponent
    <a href="{{ config('app.webpanel_url') }}">{{ config('app.webpanel_url') }}</a><br /><br />
    <p>
        Fehlerfreie Grüße,
        <br />
        dein {{ config('app.projectname') }} Team
    </p>
@endcomponent
