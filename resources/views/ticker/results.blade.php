<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticker Results</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        .header {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
        }
        .date-range {
            color: #666;
            font-size: 14px;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            transition: transform 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card-title {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .card-value {
            font-size: 32px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        .card-date {
            font-size: 13px;
            color: #999;
        }
        .low { border-left: 4px solid #e74c3c; }
        .high { border-left: 4px solid #27ae60; }
        .back-btn {
            display: inline-block;
            padding: 12px 30px;
            background: white;
            color: #667eea;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            transition: all 0.3s;
        }
        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        }
        .no-data {
            background: white;
            border-radius: 15px;
            padding: 40px;
            text-align: center;
            color: #666;
        }
        .no-data h2 {
            color: #e74c3c;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Ticker Analysis Results</h1>
            <p class="date-range">
                Showing data from <strong>{{ $startDate->format('M d, Y H:i') }}</strong> 
                to <strong>{{ $endDate->format('M d, Y H:i') }}</strong>
            </p>
        </div>

        @if($lowestBid && $lowestPrice && $highestPrice && $highestAsk)
            <div class="grid">
                <div class="card low">
                    <div class="card-title">Lowest Bid</div>
                    <div class="card-value">${{ number_format($lowestBid->bid, 2) }}</div>
                    <div class="card-date">{{ $lowestBid->time->format('M d, Y H:i') }}</div>
                </div>

                <div class="card low">
                    <div class="card-title">Lowest Market Price</div>
                    <div class="card-value">${{ number_format($lowestPrice->price, 2) }}</div>
                    <div class="card-date">{{ $lowestPrice->time->format('M d, Y H:i') }}</div>
                </div>

                <div class="card high">
                    <div class="card-title">Highest Market Price</div>
                    <div class="card-value">${{ number_format($highestPrice->price, 2) }}</div>
                    <div class="card-date">{{ $highestPrice->time->format('M d, Y H:i') }}</div>
                </div>

                <div class="card high">
                    <div class="card-title">Highest Ask</div>
                    <div class="card-value">${{ number_format($highestAsk->ask, 2) }}</div>
                    <div class="card-date">{{ $highestAsk->time->format('M d, Y H:i') }}</div>
                </div>
            </div>
        @else
            <div class="no-data">
                <h2>⚠️ No Data Available</h2>
                <p>No ticker data found for the selected date range.</p>
                <p style="margin-top: 10px;">Please try different dates or wait for data to be collected.</p>
            </div>
        @endif

        <a href="{{ route('ticker.index') }}" class="back-btn">← Search Again</a>
    </div>
</body>
</html>