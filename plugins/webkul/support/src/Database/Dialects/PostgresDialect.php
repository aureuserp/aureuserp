<?php

namespace Webkul\Support\Database\Dialects;

use Illuminate\Support\Facades\DB;

class PostgresDialect implements DatabaseDialect
{
    public function jsonArrayAgg(string $column): string
    {
        return "json_agg({$column})";
    }

    public function monthBucket(string $column): string
    {
        return "to_char({$column}, 'YYYY-MM')";
    }

    public function alterColumnType(string $table, string $column, string $blueprintMethod, string $postgresType, string $postgresUsing): void
    {
        DB::statement("ALTER TABLE {$table} ALTER COLUMN {$column} TYPE {$postgresType} USING {$postgresUsing}");
    }

    public function caseInsensitiveEquals(string $column): string
    {
        return "LOWER({$column}) = LOWER(?)";
    }

    public function syncSequences(): void
    {
        DB::statement(<<<'SQL'
            DO $$
            DECLARE
                tbl RECORD;
                seq text;
                current_max bigint;
            BEGIN
                FOR tbl IN
                    SELECT table_name FROM information_schema.columns
                    WHERE table_schema = 'public' AND column_name = 'id'
                LOOP
                    seq := pg_get_serial_sequence(format('%I', tbl.table_name), 'id');

                    IF seq IS NOT NULL THEN
                        EXECUTE format('SELECT COALESCE(max(id), 0) FROM %I', tbl.table_name) INTO current_max;

                        IF current_max > 0 THEN
                            PERFORM setval(seq, current_max, true);
                        ELSE
                            PERFORM setval(seq, 1, false);
                        END IF;
                    END IF;
                END LOOP;
            END $$;
            SQL);
    }
}
