# Laravel AI API

Laravel API med Ollama Mistral integration, autentisering och session-baserad chatthistorik.

## Funktioner

- Laravel Sanctum autentisering
- Ollama Mistral LLM integration
- Session-baserad chatthistorik med UUID
- Konversationskontext (AI kommer ihåg tidigare meddelanden)
- Guest chat utan autentisering


### 1. Installera Ollama
```bash
# Ladda ner från: https://ollama.com/download
ollama pull mistral
ollama serve  # Håll denna terminal öppen
```

### 2. Setup Laravel
```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate
```

### 3. Starta server
```bash
cd public
php -S localhost:8000
```

API körs nu på http://localhost:8000

## API Endpoints

### Autentisering
```http
POST /api/register  # Registrera
POST /api/login     # Logga in (få Bearer token)
POST /api/logout    # Logga ut
GET  /api/me        # Hämta användarinfo
```

### Chat
```http
POST /api/chat
POST /api/chat (med session_id)
```

```http
GET    /api/sessions
GET    /api/sessions/{id}
DELETE /api/sessions/{id}
DELETE /api/sessions
```

### Registrera
```json
POST /api/register
{
  "name": "Test User",
  "email": "test@test.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

### Logga in
```json
POST /api/login
{
  "email": "test@test.com",
  "password": "password123"
}
```

### Chat (ny session)
```json
POST /api/chat
Authorization: Bearer {token}
{
  "message": "Vad är Laravel?"
}
```

### Fortsätt session
```json
POST /api/chat
Authorization: Bearer {token}
{
  "message": "Ge mig ett exempel",
  "session_id": "{session_id}"
}
```

## Testning

Använd Insomnia
