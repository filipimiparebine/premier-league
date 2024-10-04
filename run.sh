#!/bin/bash

composer i
npm i --prefix frontend
cp .env.example .env
docker compose up --build
