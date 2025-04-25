class TransactionHandler {
    constructor() {
        this.token = null;
        this.tokenExpiresAt = null;
        this.tokenEndpoint = '/merchant/transaction/token';
        this.transactionEndpoint = '/merchant/transactions';
    }

    async generateToken() {
        try {
            const response = await fetch(this.tokenEndpoint);
            const data = await response.json();

            if (response.ok) {
                this.token = data.token;
                this.tokenExpiresAt = new Date(data.expires_at);
                return this.token;
            } else {
                throw new Error('Failed to generate token');
            }
        } catch (error) {
            console.error('Error generating token:', error);
            throw error;
        }
    }

    async processTransaction(transactionData) {
        if (!this.token || new Date() > this.tokenExpiresAt) {
            await this.generateToken();
        }

        try {
            const response = await fetch(this.transactionEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Transaction-Token': this.token,
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(transactionData)
            });

            const data = await response.json();

            if (response.ok) {
                // Clear token after successful transaction
                this.token = null;
                this.tokenExpiresAt = null;
                return data;
            } else if (response.status === 409) {
                // Token already used, generate new one and retry
                await this.generateToken();
                return this.processTransaction(transactionData);
            } else {
                throw new Error(data.message || 'Transaction failed');
            }
        } catch (error) {
            console.error('Error processing transaction:', error);
            throw error;
        }
    }
}

// Initialize transaction handler
const transactionHandler = new TransactionHandler();

// Example usage:
/*
async function handleTransaction() {
    try {
        const transactionData = {
            user_id: 'user-uuid',
            items: [
                { product: 1, qty: 2 },
                { product: 2, qty: 1 }
            ]
        };

        const result = await transactionHandler.processTransaction(transactionData);
        console.log('Transaction successful:', result);
    } catch (error) {
        console.error('Transaction failed:', error);
    }
}
*/
