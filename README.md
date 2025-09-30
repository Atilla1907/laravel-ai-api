# Laravel AI API

Laravel API som integrerar Ollama (Mistral LLM) med autentisering och session-baserad chatthistorik.

## Funktioner

- ✅ Laravel Sanctum autentisering (register, login, logout)
- ✅ Ollama Mistral LLM integration
- ✅ Session-baserad chatthistorik med UUID
- ✅ AI kommer ihåg tidigare meddelanden i sessionen
- ✅ Guest chat (fungerar utan autentisering)

## Teknologier

- Laravel 10
- Laravel Sanctum
- Ollama (Mistral)
- MySQL
- PHP 8.1+

## Installation

### 1. Installera Ollama
```bash
# Ladda ner från: https://ollama.com/download
ollama pull mistral
ollama serve  # Håll denna terminal öppen
```

### 2. Setup Laravel
```bash
composer install
copy .env.example .env  # Windows
cp .env.example .env    # Mac/Linux
php artisan key:generate
```

### 3. Konfigurera .env
```env
DB_DATABASE=laravel_ai_api
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 4. Kör migrationer
```bash
# Skapa databas först
CREATE DATABASE laravel_ai_api;

# Sedan kör migrationer
php artisan migrate
```

### 5. Starta server
```bash
php artisan serve
# API: http://localhost:8000
```

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
POST /api/chat                    # Guest chat (inget sparas)
POST /api/chat                    # Ny session (autentiserad)
POST /api/chat + session_id       # Fortsätt session (AI kommer ihåg)
```

### Sessions
```http
GET    /api/sessions              # Lista alla sessioner
GET    /api/sessions/{id}         # Hämta session historik
DELETE /api/sessions/{id}         # Radera session
DELETE /api/sessions              # Radera alla sessioner
```

## Exempel

### 1. Registrera och logga in
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Test","email":"test@test.com","password":"password123","password_confirmation":"password123"}'

curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@test.com","password":"password123"}'
# Spara access_token från svaret
```

### 2. Chat (ny session)
```bash
curl -X POST http://localhost:8000/api/chat \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {TOKEN}" \
  -d '{"message":"Vad är Laravel?"}'
# Spara session_id från svaret
```

### 3. Fortsätt session
```bash
curl -X POST http://localhost:8000/api/chat \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {TOKEN}" \
  -d '{"message":"Ge mig ett exempel","session_id":"{SESSION_ID}"}'
```

## Databas

**users:** id, name, email, password, timestamps

**chat_histories:** id, user_id (nullable), session_id (UUID), user_message, bot_response, timestamps

## Testning

Importera `insomnia-collection.json` i Insomnia/Postman för att testa alla endpoints.

## Felsökning

**Ollama svarar inte:**
```bash
ollama serve  # Starta Ollama
```

**Databas fel:**
```bash
php artisan migrate:fresh
```

**Token fungerar inte:**
Logga in igen för ny token.
