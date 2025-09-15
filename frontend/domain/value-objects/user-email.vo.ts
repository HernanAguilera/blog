export class UserEmail {
    private readonly _value: string;

    constructor(value: string) {
        if (!value || value.trim() === '') {
            throw new Error('Email cannot be empty');
        }

        const normalizedEmail = value.trim().toLowerCase();

        // Basic email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(normalizedEmail)) {
            throw new Error('Email format is invalid');
        }

        // Additional validations
        if (normalizedEmail.length > 254) {
            throw new Error('Email is too long');
        }

        this._value = normalizedEmail;
    }

    public value(): string {
        return this._value;
    }

    public domain(): string {
        return this._value.split('@')[1] || '';
    }

    public localPart(): string {
        return this._value.split('@')[0] || '';
    }

    public equals(other: UserEmail): boolean {
        return this._value === other._value;
    }

    public toString(): string {
        return this._value;
    }
}