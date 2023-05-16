<!DOCTYPE html>
<html>
<head>
    <title>Umsetzungsfreigabe</title>
</head>
<body>
	<header>
		<div>
			<h1>BugShot</h1><br /><br />
			<div>
				<span>Erstellungsdatum: {{ now()->format('d.m.Y') }}</span><br />
				<span>Report Nr.: {{ $reportId }}</span><br />
			</div>
		</div>
	</header>

	<main>
		@foreach($bugs as $bug)
			<div class="bug-card">
				<div class="bug-details">
					<h2>{{ $bug["designation"] }}</h2>
					<div>
						<h4>ID:</h4> {{ $bug["id"] }}<br />
						<h4>Erstellt von:</h4> {{ $bug["user_id"] }}
					</div>
					<p>
						<h4>Beschreibung:</h4>
						{{ $bug["description"] }}
					</p>
					<div>
						<h4>Priorität:</h4>
						{{ $bug["priority_id"] }}<br />
						<h4>Status:</h4>
						{{ $bug["status_id"] }}
					</div>
					<div>
						<h4>Deadline:</h4>
						{{ $bug["deadline"] }}
					</div>
					<div>
						<h4>Zeiteinschätzung:</h4>
						{{ $bug["time_estimation"] ? $bug["time_estimation"] : "Keine Zeiteinschätzung vorhanden" }} {{ $bug["approval_status_id"] }}
					</div>
				</div>
				<div class="bug-screenshot">

				</div>
				<div class="bug-signatures">
					<div>Bearbeiter: ____________</div>
				</div>
			</div>
		@endforeach
	</main>

	<footer>

	</footer>
</body>
</html>

