# Chat de atención

Aplicación para guardar prospectos, citas y conversaciones, y responder con un modelo de lenguaje después de iniciar sesión.

## Qué incluye

- Negocios, prospectos, citas y conversaciones
- Entrada de mensajes de WhatsApp
- Chat con Groq, protegido con Sanctum
- La pregunta se guarda en el historial

El chat responde la pregunta directa. No arma contexto de documentos mientras no exista una fuente propia de embeddings.

## Stack

Laravel, React, PostgreSQL y Sanctum.

## Cómo correrlo

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
php artisan serve
```

Abre `/chat`, crea una cuenta y envía un mensaje. La clave de Groq va en `.env` (`GROQ_API_KEY`). No subas ese archivo.

PostgreSQL tiene que estar en marcha. Redis solo hace falta si vas a procesar colas.
