# Anton Sadovnikov
**CTO / Lead Backend Developer**

Bangkok, Thailand · sadovnikoff@gmail.com · +6281945575322 · [LinkedIn](https://linkedin.com/in/siddthartha) · [GitHub](https://github.com/siddthartha) · [sadovnikov.space](http://sadovnikov.space/default/docs)

---

## Summary

Backend developer with 20+ years of experience, specialising in PHP 8+ using Yii, Laravel, Lumen, and pure PSR-style PHP. Strong mathematical background and a formal degree in software engineering.

Focused on building scalable, asynchronous (Swoole / RoadRunner), and fault-tolerant systems. Actively interested in Rust, machine learning, GIS systems, IoT services, and agentic development. Open-source contributor.

Actively leverages AI coding agents in day-to-day development and explores productivity patterns for AI-assisted workflows. Ready to introduce these practices to small teams or take on a Staff Engineer role for an AI-native product.

Targeting **Tech Lead**, **Lead Developer**, or **AI Staff Engineer** positions. Interested in modern, technically ambitious products and working alongside strong professionals.

Prefers permanent or long-term project (1+ year) fully remote full-time employment ONLY. 

English: EFSET 54 Upper Intermediate (CEFR B2). 
Based in Bangkok, available to work MSK/European timezones.

#### HR FAQ

**Why have you stayed less than two years in most positions? Why are you currently looking for a new opportunity?**
* Most of my experience has been in **contract-based software development**, primarily working with startups and early-stage companies.
* I have been working **fully remotely by choice since the early 2000s**, long before remote work became mainstream.
* I am **open to long-term collaboration (2+ years)** when there are challenging technical problems, meaningful impact, and opportunities to contribute to the company's growth. 

---

## Experience

### Lead Backend Developer / Technical Lead — ApolloRise Tech
*Nov 2025 – May 2026*

Cloud-based EdTech platform for foreign language learning: media handling, S3 cloud storage, interactive exercises, course purchases via Stripe.

- AI-assisted development using *Qwen Code* and *Claude Code* agents
- Integrated into Agile workflow with CI/CD pipelines
- Delivered to production within 2 months with continuous feature delivery
- Full REST-like JSON API covered with PHPUnit tests and up-to-date OpenAPI documentation

Stack: `PHP`, `Laravel`, `PostgresSQL`, `Redis`, `MinIO` 

---

### Lead Backend Developer / Tech Lead — [Hyperspace.ai](https://hyperspace.ai)
*Jun 2024 – Oct 2025*

Built an AI platform aggregating popular ML models into a unified web service and mobile application.

- Configured containerisation and CI/CD pipelines (GitHub Actions)
- Implemented API integration tests for CI
- Centralised logging via New Relic
- Integrated notifications from all sources into Telegram via n8n
- Redis-based task queue management with dedicated ML workers
- Built a custom multi-model agent pipeline (GPT-4o · GPT-4o-mini · Flux.1 · SDXL) to generate 10,000 chatbot personas on demand — covering character backstory, system prompts, and AI-generated portraits
- Deployed an internal token smart contract to the Ethereum testnet

Stack: `PHP`, `Laravel`, `PostgresSQL`, `Redis`, `Google Cloud` 

---

### Lead Backend Developer — Clivetor
*Jan 2023 – Apr 2024*

Designed and developed an image generation service based on StableDiffusion manually hosted in RunPod.

- Built a Lumen / Swoole microservice with API for running ML pipelines in a serverless environment (RunPod.io): SD, upscale, age detection, face swap, and others

---

### Lead Backend Developer / CTO — FIT Studio (Startup Development Studio)
*Jun 2021 – Jan 2023*

CTO role: team hiring, process setup, technical leading, production launches across multiple startup projects.

- **[GetPower](https://getpower.ru)** — power bank rental vending service. Async agent system: dispensing stations + mobile clients. Communication via MQTT over Redis. Containerisation, ELK logging, async debugging, production deployment.
- **[Loginio](https://loginio.com)** — public and rental transport search service. Team hiring, architecture design, MVP implementation. 
Stack: `PHP`, `PostgreSQL`, `PostGIS`, `Redis`, `WebSockets`
- **[GolosOnline](https://golosonline.com)** — legally significant online voting service. Cloud solution with qualified electronic signatures and blockchain. Deployed HyperLedger cluster, integrated GOST cryptography, set up GitLab CI/CD. 
Stack: `PHP`, `Etherium`, `CryptoPro`, `Node.js`, `Redis`, `MongoDB`
- **[24Service](https://24servis.online)** — cloud access control system. Video stream monitoring (OpenVidu) and barrier management. Containerisation, API standardisation, OpenVidu client debugging in Dart. 
Stack: `PHP`, `Dart`, `JavaScript`, `WebSockets`

---

### Backend Developer (Contract) — Various Projects
*Feb 2016 – Sep 2019*

Short-term contracts and project-based engagements across fintech, fitness, and utilities sectors.

- **GlobalPayments** *(Berlin)* — Banking notification service for credit status updates. Resolved billing data integrity issues, queue prioritisation and logging, storage layer unification. Stack: `PHP` `Yii` `MySQL` `RabbitMQ`
- **4warranty.ru** *(Moscow)* — CRM for a warranty repair intermediary between mobile device manufacturers and service centres. Implemented billing, logging, external API integrations, and reporting subsystem. Stack: `PHP` `Yii2`
- **FITBAR / sportmenu.com** *(Moscow)* — Backend architecture and development for a sports nutrition marketplace. Stack: `PHP` `Yii2` `MySQL`

---

### Tech Lead — [Perare](https://perare.io)
*Oct 2019 – May 2021*

Greenfield geospatial startup. Built a land plot sales platform powered by Indonesia's official cadastral registry — and outperformed the government service in both speed and reliability.

- Developed a multithreaded distributed crawler to scrape 70 million geo-polygons from the official registry via proxy rotation; raw data converted from WGS84, validated, cleaned, fixed, and indexed into Elasticsearch
- PHP workers consumed the Elasticsearch index and persisted polygons into PostgreSQL with PostGIS spatial indexes, with metadata stored separately
- Live geosearch across 70M polygons returned results in under 500ms with no caching or vector tile layers
- Under load the service degraded gracefully (slower but alive) while the official government registry went down entirely
- Shipped to production in 4 months; UX and performance surpassed the state cadastral service
- Startup survived and pivoted to geo-analytics

Stack: `PHP` `Yii` `PostgreSQL` `PostGIS` `Elasticsearch` `OpenStreetMap` `MapBox` `GeoJSON` `Node.js` `Docker`

---

### Lead Developer — MoyZhKH
*Nov 2018 – Sep 2019*

Inherited a failing CRM for housing & utilities management companies — 3 years and 15 engineers had produced a system too slow to use in production. Brought in by the CEO after the entire team was let go.

- Conducted a full technical audit over one sprint to assess viability; recommended refactor over rewrite and assembled a team of 4 (senior frontend, senior backend, mid-level dev, systems analyst)
- Bottleneck #1: no engineering process — no containerisation, no CI/CD, no async collaboration; set up Docker, Bitbucket Pipelines, and a full Agile workflow for a distributed remote team, which proved more effective than their previous 15-person on-site setup
- Bottleneck #2: no job queue — EPD generation across thousands of apartments per management company had to complete within a strict post-period accounting window; sequential processing made this impossible; resolved with RabbitMQ queue-based distributed computation with parallel job processing across dedicated workers
- Bottleneck #3: monolithic SPA with deeply nested tables and forms taking minutes to load; decomposed into independently loadable modules via webpack code splitting and lazy API loading — effectively a micro-frontend architecture
- Bottleneck #4: single shared database made onboarding new management companies a scaling risk; introduced PostgreSQL sharding via a custom Yii2 behavior integrated transparently into the ORM layer — effectively transforming the product from a single-tenant CRM into a scalable multi-tenant cloud platform capable of serving thousands of companies
- Delivered full refactor in 8 months against a 6–12 month estimate; successfully onboarded first external management companies post-launch — the primary business goal that had been blocked for years

Stack: `Yii2` `PostgreSQL` `RabbitMQ` `Docker` `AureliaJS` `Bitbucket Pipelines`

---

### Backend PHP Developer — Bolshaya Zemlya LLC
*Oct 2015 – Feb 2016*

Developed a geo-service ([bigland.ru](https://bigland.ru)): Yandex Maps / Google Maps integration, WGS84 coordinate converter, cadastral polygon rendering.

---

### Freelance Web Developer
*Mar 2005 – Sep 2014*

10+ years of web development across various freelance projects.

---

### Senior Systems Engineer / Programmer — UNET (IPS)
*Jan 1999 – Sep 2002*

Built a prepaid traffic accounting system for a local ISP (Perl + MySQL, ipchains).

---

## Skills

| Category | Technologies |
|---|---|
| **Languages** | `PHP 8+` `Rust` `Node.js` `Shell` `C++` |
| **Frameworks** | `Yii` `Laravel` `Lumen` `Swoole` `RoadRunner` |
| **Databases** | `PostgreSQL` `MySQL` `MongoDB` `PostGIS` `MinIO` |
| **Queue / Cache / Search** | `Redis` `RabbitMQ` `Elasticsearch` `Sphinx` |
| **DevOps** | `Docker` `Docker Compose` `Kubernetes` `Linux` `GitHub Actions` `GitLab CI` `Bitbucket Pipelines` `ELK` `New Relic` `n8n` |
| **Cloud** | `AWS` `Google Cloud` `RunPod` |
| **Architecture** | `OOP` `async programming` `microservices` `REST API` `WebSockets` `MQTT` |
| **AI / ML** | `OpenAI API` `StableDiffusion` `ML pipelines` `agentic systems` `AI-assisted development` `Claude Code` `Qwen Code` |
| **Blockchain** | `HyperLedger` `Ethereum` `Solidity` |
| **GIS** | `PostGIS` `OpenStreetMap` `MapBox` `GeoJSON` |

---

## Education

**Volodymyr Dahl East Ukrainian National University**

Specialisation: Applied Programmer / Systems Programmer · 1998 – 2003

---

## Languages

- **English** — B2 Upper Intermediate (EFSET 54)
- **Russian / Ukrainian** — Native