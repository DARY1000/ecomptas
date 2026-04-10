-- Initialisation PostgreSQL — ComptaSaaS
-- Ce script s'exécute UNE SEULE FOIS au premier démarrage du conteneur postgres

-- Extension UUID
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- Extension pour recherche full-text (utile pour recherche factures)
CREATE EXTENSION IF NOT EXISTS "pg_trgm";

-- Paramètres de performance
ALTER SYSTEM SET shared_buffers = '256MB';
ALTER SYSTEM SET effective_cache_size = '512MB';
ALTER SYSTEM SET work_mem = '16MB';
ALTER SYSTEM SET maintenance_work_mem = '64MB';

-- Timezone Benin
ALTER DATABASE compta_saas SET timezone TO 'Africa/Porto-Novo';
