export class UserId {
    private readonly _value: string;

    constructor(value: string | number) {
        // Convert to string and validate
        const idString = typeof value === 'string' ? value : String(value);

        if (!idString || idString.length === 0) {
            throw new Error('User ID cannot be empty');
        }

        // For now, accept both numeric IDs and UUIDs to be compatible with the backend
        // In the future, this could be migrated to only accept UUIDs
        this._value = idString;
    }

    public value(): string {
        return this._value;
    }

    public equals(other: UserId): boolean {
        return this._value === other._value;
    }

    public toString(): string {
        return this._value;
    }
}