<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rapport de performance communal</title>
    <style>
        :root {
            --ink: #102A43;
            --muted: #486581;
            --line: #BCCCDC;
            --soft: #EAF3F8;
            --brand: #005F73;
            --brand-2: #0A9396;
            --danger: #B42318;
            --success: #047857;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f4f8fb;
            color: var(--ink);
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.45;
        }

        .page {
            width: min(1100px, calc(100% - 32px));
            margin: 24px auto;
            background: white;
            border: 1px solid var(--line);
            padding: 32px;
        }

        .toolbar {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            width: min(1100px, calc(100% - 32px));
            margin: 18px auto 0;
        }

        .button {
            border: 1px solid var(--brand);
            border-radius: 6px;
            padding: 10px 14px;
            background: var(--brand);
            color: white;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }

        .button.secondary {
            background: white;
            color: var(--brand);
        }

        header {
            display: flex;
            justify-content: space-between;
            gap: 24px;
            border-bottom: 3px solid var(--brand);
            padding-bottom: 18px;
        }

        h1,
        h2,
        h3,
        p {
            margin: 0;
        }

        h1 {
            color: var(--brand);
            font-size: 28px;
        }

        h2 {
            margin-top: 28px;
            color: var(--brand);
            font-size: 20px;
        }

        .meta {
            text-align: right;
            color: var(--muted);
            font-size: 13px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-top: 16px;
        }

        .card {
            border: 1px solid var(--line);
            border-radius: 6px;
            padding: 14px;
            background: #fff;
        }

        .label {
            color: var(--muted);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .value {
            display: block;
            margin-top: 8px;
            color: var(--brand);
            font-size: 28px;
            font-weight: 800;
        }

        .danger {
            color: var(--danger);
        }

        table {
            width: 100%;
            margin-top: 14px;
            border-collapse: collapse;
            font-size: 13px;
        }

        th,
        td {
            border: 1px solid var(--line);
            padding: 9px 10px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: var(--soft);
            color: var(--brand);
            font-size: 11px;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .badge {
            display: inline-block;
            border-radius: 999px;
            padding: 3px 8px;
            background: var(--soft);
            color: var(--brand);
            font-weight: 700;
            font-size: 12px;
        }

        .summary {
            margin-top: 18px;
            border-left: 4px solid var(--brand-2);
            background: var(--soft);
            padding: 14px 16px;
            color: var(--ink);
            font-size: 14px;
        }

        @media print {
            body {
                background: white;
            }

            .toolbar {
                display: none;
            }

            .page {
                width: 100%;
                margin: 0;
                border: 0;
                padding: 0;
            }

            h2 {
                break-after: avoid;
            }

            table,
            .card {
                break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <a class="button secondary" href="{{ route('incidents.index') }}#stats">Retour</a>
        <button class="button" type="button" onclick="window.print()">Imprimer / PDF</button>
    </div>

    <main class="page">
        <header>
            <div>
                <h1>Rapport de performance communal</h1>
                <p>{{ $commune?->name ?: 'Commune non définie' }}{{ $commune?->department ? ' - '.$commune->department : '' }}</p>
            </div>
            <div class="meta">
                <p>Généré le {{ $generatedAt->format('d/m/Y H:i') }}</p>
                <p>Responsable : {{ $user->name }}</p>
            </div>
        </header>

        <section>
            <h2>Indicateurs clés</h2>
            <div class="grid">
                <div class="card">
                    <span class="label">Incidents</span>
                    <strong class="value">{{ $stats['total'] }}</strong>
                </div>
                <div class="card">
                    <span class="label">Taux résolution</span>
                    <strong class="value">{{ $performanceStats['resolutionRate'] }}%</strong>
                </div>
                <div class="card">
                    <span class="label">Temps moyen total</span>
                    <strong class="value">{{ $performanceStats['averageResolutionHours'] !== null ? $performanceStats['averageResolutionHours'].' h' : 'N/A' }}</strong>
                </div>
                <div class="card">
                    <span class="label">Dossiers > 48h</span>
                    <strong class="value {{ $performanceStats['late'] > 0 ? 'danger' : '' }}">{{ $performanceStats['late'] }}</strong>
                </div>
            </div>

            <div class="grid">
                <div class="card">
                    <span class="label">En attente</span>
                    <strong class="value">{{ $stats['pending'] }}</strong>
                </div>
                <div class="card">
                    <span class="label">En cours</span>
                    <strong class="value">{{ $stats['progress'] }}</strong>
                </div>
                <div class="card">
                    <span class="label">En validation</span>
                    <strong class="value">{{ $stats['validation'] }}</strong>
                </div>
                <div class="card">
                    <span class="label">Résolus</span>
                    <strong class="value">{{ $stats['resolved'] }}</strong>
                </div>
            </div>

            <p class="summary">
                Le rapport permet à l'administrateur communal d'évaluer le volume des signalements, la rapidité de traitement et la performance des agents techniques.
            </p>
        </section>

        <section>
            <h2>Répartition par catégorie</h2>
            <table>
                <thead>
                    <tr>
                        <th>Catégorie</th>
                        <th>Total</th>
                        <th>Résolus</th>
                        <th>Taux</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categoryStats as $row)
                        <tr>
                            <td>{{ $row['label'] }}</td>
                            <td>{{ $row['count'] }}</td>
                            <td>{{ $row['resolved'] }}</td>
                            <td>{{ $row['count'] > 0 ? round(($row['resolved'] / $row['count']) * 100) : 0 }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">Aucune donnée par catégorie.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <section>
            <h2>Performance des agents</h2>
            <table>
                <thead>
                    <tr>
                        <th>Agent</th>
                        <th>Affectées</th>
                        <th>En cours</th>
                        <th>Résolues</th>
                        <th>Taux</th>
                        <th>Temps moyen</th>
                        <th>&gt; 48h</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($agentStats as $row)
                        <tr>
                            <td>{{ $row['name'] }}</td>
                            <td>{{ $row['assigned'] }}</td>
                            <td>{{ $row['progress'] }}</td>
                            <td>{{ $row['resolved'] }}</td>
                            <td>{{ $row['resolutionRate'] }}%</td>
                            <td>{{ $row['averageResolutionHours'] !== null ? $row['averageResolutionHours'].' h' : 'N/A' }}</td>
                            <td><span class="badge">{{ $row['late'] }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">Aucun agent technique enregistré.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <section>
            <h2>Dernières interventions traitées</h2>
            <table>
                <thead>
                    <tr>
                        <th>Incident</th>
                        <th>Catégorie</th>
                        <th>Zone</th>
                        <th>Agent</th>
                        <th>Résolu le</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentResolvedIncidents as $incident)
                        <tr>
                            <td>{{ $incident->title }}</td>
                            <td>{{ $incident->categoryLabel() }}</td>
                            <td>{{ $incident->district }}</td>
                            <td>{{ $incident->assignedAgent?->name ?: 'Non affecté' }}</td>
                            <td>{{ ($incident->resolved_at ?? $incident->updated_at)->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">Aucune intervention traitée pour le moment.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
