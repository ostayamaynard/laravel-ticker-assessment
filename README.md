# Laravel BTC-USDT Ticker Assessment

A Laravel application that fetches BTC-USDT cryptocurrency ticker data from Coinbase API every 15 minutes, calculates bid/ask differences, stores everything in MySQL, and provides a web interface to search and analyze historical data.

---

## Requirements Met

- ✅ Fetch data every 15 minutes from specified Coinbase API
- ✅ Store all API fields in MySQL database
- ✅ Calculate: ask - bid difference
- ✅ Calculate: ask - lastPrice difference
- ✅ Calculate: bid - lastPrice difference
- ✅ Use queued event listeners for data processing
- ✅ Web page with two date/time inputs
- ✅ Display lowest BID with date/time
- ✅ Display lowest Market Price with date/time
- ✅ Display highest Market Price with date/time
- ✅ Display highest ASK with date/time

---

## Tech Stack

- **Laravel 10.x** - PHP Framework
- **MySQL** - Database
- **Queue System** - For asynchronous processing
- **Laravel Scheduler** - For automated 15-minute data fetching
- **HTTP Client** - For API requests
- **Blade Templates** - For views

---

## Installation

### Prerequisites

- PHP 8.1 or higher
- Composer
- MySQL 5.7 or higher
- VPN (if accessing from Philippines due to ISP blocking)

### Setup Steps

**1. Clone the repository**
```bash
git clone https://github.com/YOUR_USERNAME/laravel-ticker-assessment.git
cd laravel-ticker-assessment
```

**2. Install dependencies**
```bash
composer install
```

**3. Create environment file**
```bash
cp .env.example .env
```

**4. Configure database**

Edit `.env` file and set your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ticker_db
DB_USERNAME=your_username
DB_PASSWORD=your_password

QUEUE_CONNECTION=database
```

**5. Generate application key**
```bash
php artisan key:generate
```

**6. Create database**

Open MySQL and create the database:
```sql
CREATE DATABASE ticker_db;
```

**7. Run migrations**
```bash
php artisan migrate
php artisan queue:table
php artisan migrate
```

---

## Running the Application

You need **three separate terminal windows**:

### Terminal 1: Web Server
```bash
php artisan serve
```

### Terminal 2: Queue Worker

This is **required** for the queued event listeners to process data.
```bash
php artisan queue:work
```

### Terminal 3: Scheduler

This runs the 15-minute data fetching schedule.
```bash
php artisan schedule:work
```

---

## Testing

### Test Data Fetch Manually
```bash
php artisan ticker:fetch
```

### Verify Data in Database
```bash
php artisan tinker
```

Then run:
```php
App\Models\TickerData::count()
App\Models\TickerData::latest()->first()
```

---

## Using the Web Interface

1. Open your browser to: **http://localhost:8000**

2. You'll see a search form with two inputs:
   - Start Date & Time
   - End Date & Time

3. Enter your desired date range

4. Click **"Search Ticker Data"**

5. View the results showing:
   - Lowest BID during the period with date/time
   - Lowest Market Price during the period with date/time
   - Highest Market Price during the period with date/time
   - Highest ASK during the period with date/time

---

## Project Structure
```
laravel-ticker-assessment/
│
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       └── FetchTickerData.php          # Command to fetch API data
│   │
│   ├── Events/
│   │   └── TickerDataFetched.php            # Event triggered after fetch
│   │
│   ├── Listeners/
│   │   └── SaveTickerData.php               # Queued listener for calculations
│   │
│   ├── Models/
│   │   └── TickerData.php                   # Database model
│   │
│   └── Http/
│       └── Controllers/
│           └── TickerController.php         # Web controller
│
├── database/
│   └── migrations/
│       └── create_ticker_data_table.php     # Database schema
│
├── resources/
│   └── views/
│       └── ticker/
│           ├── search.blade.php             # Search form
│           └── results.blade.php            # Results page
│
└── routes/
    └── web.php                              # Web routes
```

---

## How It Works

### Data Collection (Every 15 Minutes)

1. Laravel Scheduler runs `ticker:fetch` command every 15 minutes
2. Command fetches data from Coinbase API endpoint
3. Triggers `TickerDataFetched` event with raw API data

### Data Processing (Queued)

1. `SaveTickerData` listener receives the event
2. Processes asynchronously via queue worker
3. Calculates three required differences:
   - `ask - bid`
   - `ask - lastPrice`
   - `bid - lastPrice`
4. Saves all fields to MySQL database

### Web Interface

1. User enters two date/time values
2. Controller queries database for records in date range
3. Finds and displays four statistics:
   - Lowest BID with timestamp
   - Lowest Market Price with timestamp
   - Highest Market Price with timestamp
   - Highest ASK with timestamp

---

## Production Deployment

### Setup Cron Job

For Linux/Mac production servers:
```bash
crontab -e
```

Add this line:
```
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

### Setup Queue Worker with Supervisor

Create supervisor config file `/etc/supervisor/conf.d/ticker-worker.conf`:
```ini
[program:ticker-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path-to-project/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
numprocs=1
redirect_stderr=true
stdout_logfile=/path-to-project/storage/logs/worker.log
```

Then:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start ticker-worker:*
```

### Optimize Laravel
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Troubleshooting

### API Access Issues (Philippines)

The Coinbase API is blocked by some Philippine ISPs (PLDT/Smart).

**Solution: Use a VPN**

1. Install **Cloudflare WARP**: https://1.1.1.1/
2. Connect to VPN
3. Test API access:
```bash
   curl https://api.exchange.coinbase.com/products/BTC-USDT/ticker
```
4. Run the fetch command:
```bash
   php artisan ticker:fetch
```

### Queue Not Processing

Make sure the queue worker is running:
```bash
php artisan queue:work
```

Check for failed jobs:
```bash
php artisan queue:failed
```

### Scheduler Not Running

**For Development:**
```bash
php artisan schedule:work
```

**For Production:**

Make sure cron job is properly configured (see Production Deployment section above).

---

## Database Schema

The `ticker_data` table structure:

| Column | Type | Description |
|--------|------|-------------|
| `id` | BIGINT UNSIGNED | Primary key |
| `ask` | DECIMAL(20,8) | Ask price from API |
| `bid` | DECIMAL(20,8) | Bid price from API |
| `volume` | DECIMAL(20,8) | Trading volume |
| `trade_id` | VARCHAR(255) | Trade ID from API |
| `price` | DECIMAL(20,8) | Last price (lastPrice) |
| `size` | DECIMAL(20,8) | Trade size |
| `time` | TIMESTAMP | Timestamp from API |
| `ask_bid_diff` | DECIMAL(20,8) | Calculated: ask - bid |
| `ask_last_diff` | DECIMAL(20,8) | Calculated: ask - lastPrice |
| `bid_last_diff` | DECIMAL(20,8) | Calculated: bid - lastPrice |
| `created_at` | TIMESTAMP | Record creation time |
| `updated_at` | TIMESTAMP | Record update time |

**Indexes:**
- Primary key on `id`
- Index on `time` for efficient date range queries

---

## API Information

### Endpoint
```
https://api.exchange.coinbase.com/products/BTC-USDT/ticker
```

### Response Fields Stored

**From API:**
- `ask` - Current ask price
- `bid` - Current bid price
- `price` - Last market price (lastPrice)
- `volume` - 24-hour trading volume
- `size` - Last trade size
- `trade_id` - Unique trade identifier
- `time` - Timestamp in ISO format

**Calculated:**
- `ask_bid_diff` - Spread between ask and bid
- `ask_last_diff` - Difference between ask and last price
- `bid_last_diff` - Difference between bid and last price

---

## License

This project is created for assessment purposes.

---

## Author

**Maynard**  
Full Stack Developer  
GitHub: [@YOUR_USERNAME](https://github.com/YOUR_USERNAME)

---

## Questions or Issues?

If you encounter any problems during installation or have questions about the implementation, please open an issue on GitHub or contact me directly.
