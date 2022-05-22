class Validator {
    constructor() {
        this.fields = {}
        this.messagesShown = false
        this.rules = {
            required: {
                message: 'Kolom harus diisi',
                validate: (value) => !this.isBlank(value),
            },
            email: {
                message: 'Email tidak valid',
                validate: (value) => {
                    if (!value) return true

                    return this.testRegex(value,/^[A-Z0-9.!#$%&'*+-/=?^_`{|}~]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i)
                }
            },
            phone: {
                message: 'No. Telepon tidak valid',
                validate: (value) => {
                    if (!value) return true

                    return this.testRegex(value, /^(\+?\d{0,4})?\s?-?\s?(\(?\d{3}\)?)\s?-?\s?(\(?\d{3}\)?)\s?-?\s?(\(?\d{4}\)?)$/) && !this.testRegex(value, /^\b(\d)\1{8,}\b$/)
                }
            }
        }
    }

    message(field, value, rules) {
        for (let rule of rules) {
            this.fields[field] = true
    
            if (!this.rules[rule].validate(value)) {
                this.fields[field] = false
                
                if (this.messagesShown) {
                    return this.rules[rule].message
                }
            }
        }
    }

    isAllValid() {
        for (let field in this.fields) {
            if (!this.fields[field]) return false
        }

        return true
    }

    isValid(field) {
        return this.fields.hasOwnProperty(field) && this.fields[field]
    }

    showMessages() {
        this.messagesShown = true
    }

    isBlank (value) {
        return typeof(value) === 'undefined' || value === null || this.testRegex(value, /^[\s]*$/)
    }

    testRegex(value, regex) {
        return value.toString().match(regex) !== null;
    }
}

export default Validator