#!/bin/bash

composer i
npm i --prefix frontend
cp .env.example .env
cp ./frontend/.env.example ./frontend/.env
docker compose up --build
