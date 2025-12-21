# University Competition Website

A comprehensive Laravel 11-based full-stack university competition platform where universities compete through timed quizzes with real-time synchronization, custom authentication, and comprehensive admin controls.

## Features

### 🏆 Core Functionality
- **Custom Authentication System** - Non-browser based login/registration
- **Role-Based Access Control** - Admin, Exam Manager, and Regular Users
- **Real-Time Quiz System** - Synchronized questions with 30-second timers
- **Live Scoreboard** - Real-time ranking and score updates
- **Question Management** - Add, edit, delete, and import questions via Excel
- **Test Control** - Start, control progression, and end tests

### 🎯 User Roles

#### Admin
- Manage questions (CRUD operations)
- Import questions via Excel file upload
- View system statistics and analytics
- Access comprehensive admin dashboard

#### Exam Manager
- Start and control test sessions
- Navigate through questions
- Monitor participant status in real-time
- End tests and calculate final scores

#### Regular Users (University Representatives)
- Participate in quiz competitions
- View real-time scoreboard
- Receive synchronized questions and timers
- Track personal performance

### ⚡ Technical Features

#### Real-Time Functionality
- WebSocket-based real-time updates
- Live scoreboard synchronization
- Real-time question broadcasting
- Instant answer processing

#### Quiz Mechanics
- Random question selection from database
- 30-second countdown timer per question
- Multiple choice questions (A, B, C, D)
- Automatic answer validation and scoring
- Correct answer revelation after timer expiry

#### Data Management
- Comprehensive database design
- Excel import/export functionality
- Performance analytics and reporting
- Secure data handling and validation

## Technology Stack

- **Backend**: Laravel 11 with PHP 8.2
- **Database**: SQLite (development) / MySQL (production)
- **Frontend**: Blade templates with Bootstrap 5
- **Real-Time**: Laravel Events & Broadcasting
- **Excel Processing**: Maatwebsite Excel
- **Authentication**: Custom Laravel authentication
- **Styling**: Bootstrap 5 + Font Awesome icons

## Installation & Setup

### Prerequisites
- PHP 8.2+
- Composer
- SQLite/MySQL
- Web server (Apache/Nginx)

### Installation Steps

1. **Clone and Install Dependencies**
```bash
git clone <repository-url>
cd university-competition
composer install
```

2. **Environment Configuration**
```bash
cp .env.example .env
php artisan key:generate
```

3. **Database Setup**
```bash
php artisan migrate
php artisan db:seed
```

4. **Storage Setup**
```bash
php artisan storage:link
```

5. **Serve Application**
```bash
php artisan serve
```

### Default Credentials

After seeding, you can use these accounts:

#### Admin Account
- **Email**: admin@competition.com
- **Password**: password
- **Access**: Full admin panel with question management

#### Exam Manager Account
- **Email**: exam@competition.com
- **Password**: password
- **Access**: Test control and management

#### Sample University Users
- Multiple university accounts with password: `password`
- Emails follow pattern: `[university-name]@student.com`

## Usage Guide

### For Administrators

1. **Login** with admin credentials
2. **Navigate** to Admin Dashboard
3. **Manage Questions**:
   - Add individual questions manually
   - Import questions via Excel upload
   - Edit or delete existing questions
   - View question analytics

4. **Excel Import Format**:
   ```csv
   title,option_a,option_b,option_c,option_d,correct_answer,category
   What is 2+2?,3,4,5,6,B,Mathematics
   ```

### For Exam Managers

1. **Login** with exam manager credentials
2. **Start Test**:
   - Click "Start Test" to begin session
   - System creates new test environment
   - Participants can now join

3. **Control Questions**:
   - Click "Next Question" to send questions to all participants
   - Monitor participant response status
   - System randomly selects unused questions

4. **End Test**:
   - Click "End Test" to conclude session
   - Final scores are calculated automatically
   - Rankings are assigned

### For Participants

1. **Registration/Login**:
   - Register with university details
   - Login to access quiz interface

2. **Quiz Participation**:
   - Wait for test to start
   - Answer questions within 30-second timer
   - View correct answers after timer expires
   - Monitor real-time scoreboard

## Database Schema

### Core Tables

#### Users
- `id`, `name`, `email`, `password`
- `role` (user, admin, exam_manager)
- `university` (for participants)

#### Questions
- `id`, `title`, `option_a` through `option_d`
- `correct_answer` (A, B, C, D)
- `category` (optional)

#### Tests
- `id`, `status` (waiting, active, ended)
- `current_question_id`, `started_at`, `ended_at`
- `question_start_time` (Unix timestamp)

#### Answers
- `id`, `user_id`, `question_id`, `test_id`
- `selected_answer`, `is_correct`, `answered_at`

#### Scores
- `id`, `user_id`, `test_id`
- `total_score`, `correct_answers`, `total_questions`
- `rank` (assigned after test completion)

## API Endpoints

### Authentication
- `POST /login` - User login
- `POST /register` - User registration
- `POST /logout` - User logout

### Quiz
- `GET /quiz` - Display current question
- `POST /quiz/answer` - Submit answer
- `GET /quiz/waiting` - Waiting interface

### Admin
- `GET /admin` - Admin dashboard
- `GET /admin/questions` - Question management
- `POST /admin/questions` - Create question
- `POST /admin/questions/import` - Excel import

### Exam Manager
- `GET /exam-manager` - Control dashboard
- `POST /exam-manager/start-test` - Start test
- `POST /exam-manager/next-question` - Next question
- `POST /exam-manager/end-test` - End test

### Scoreboard
- `GET /scoreboard` - Display scoreboard
- `GET /scoreboard/live` - Live scoreboard data

## Real-Time Events

### QuestionStarted
- Broadcasts when new question is sent
- Contains question data and timing information
- Updates all connected clients

### AnswerSubmitted
- Fired when participant submits answer
- Updates live scoreboard
- Tracks response statistics

### TestEnded
- Broadcasts when test concludes
- Contains final rankings and winner information
- Triggers scoreboard finalization

## Configuration

### Environment Variables
```env
APP_NAME="University Competition"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite

BROADCAST_DRIVER=log
CACHE_DRIVER=file
```

### Broadcasting Configuration
For production, configure real broadcasting:
```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=your_cluster
```

## Security Features

- CSRF protection for all forms
- Input validation and sanitization
- Role-based access control
- Secure password hashing
- Session management
- Rate limiting for API endpoints

## Performance Optimization

- Database query optimization
- Efficient real-time event handling
- Caching for frequently accessed data
- Optimized file uploads
- Responsive design for mobile devices

## Development

### Adding New Features
1. Create migrations for new database tables
2. Implement models with relationships
3. Create controllers with proper validation
4. Design responsive views
5. Add real-time events if needed
6. Update routes and middleware

### Testing
```bash
php artisan test
```

### Code Quality
```bash
php artisan pint
```

## Deployment

### Production Checklist
1. Set `APP_ENV=production`
2. Set `APP_DEBUG=false`
3. Configure production database
4. Set up proper file permissions
5. Configure web server (Apache/Nginx)
6. Set up SSL certificate
7. Configure backup strategy

### Web Server Configuration

#### Apache (.htaccess)
```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>
    RewriteEngine On
    # Handle Angular and Vue.js
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

#### Nginx
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Check database configuration in `.env`
   - Ensure database file has proper permissions
   - Run migrations if tables are missing

2. **Real-Time Updates Not Working**
   - Check broadcasting configuration
   - Verify WebSocket connections
   - Check browser console for errors

3. **Excel Import Fails**
   - Ensure file format is correct (.xlsx, .xls, .csv)
   - Check column headers match expected format
   - Verify file permissions

4. **Timer Not Synchronizing**
   - Check server time synchronization
   - Verify question start timestamps
   - Check browser JavaScript execution

## Contributing

1. Fork the repository
2. Create feature branch
3. Make changes with proper testing
4. Submit pull request with description

## License

This project is licensed under the MIT License.

## Support

For support and questions:
- Check documentation first
- Review existing issues
- Create new issue with detailed description
- Include error logs and reproduction steps

---

**University Competition Platform** - Bringing universities together through competitive learning! 🏆
