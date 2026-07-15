/**
 * A key that is unique across parallel workers. Tests name their records after it, and with
 * several workers two tests routinely start inside the same millisecond — `Date.now()` alone
 * then hands both the same name and one of them dies on a unique constraint.
 *
 * The process id separates the workers (each runs in its own process) and the counter
 * separates records created within one test.
 */
let sequence = 0;

export function uniqueKey(): string {
    sequence += 1;

    return `${Date.now()}${process.pid % 1000}${sequence}`;
}
