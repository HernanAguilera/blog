export class UserId {
    private readonly _value: string;

    constructor(value: string) {
        if (!value || value.trim() === '') {
            throw new Error('User ID cannot be empty');
        }

        // Validate UUID format (basic validation)
        const uuidRegex = /^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i;
        if (!uuidRegex.test(value)) {
            throw new Error('User ID must be a valid UUID');
        }

        this._value = value;
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