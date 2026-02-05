<template>
    <div class="otp-input">
        <label v-if="label" class="block text-sm font-medium text-gray-700 mb-2 text-center">
            {{ label }}
        </label>
        
        <div class="flex justify-center space-x-3 mb-2">
            <input
                v-for="i in length"
                :key="i"
                v-model="digits[i-1]"
                type="text"
                maxlength="1"
                :placeholder="placeholderChar"
                @input="onInput(i, $event)"
                @keydown="onKeyDown(i, $event)"
                @paste="onPaste"
                @focus="onFocus(i)"
                :disabled="disabled"
                :class="[
                    'w-12 h-12 text-center text-xl font-bold border-2 rounded-lg focus:outline-none transition-colors duration-200',
                    disabled 
                        ? 'bg-gray-100 border-gray-300 text-gray-400 cursor-not-allowed' 
                        : digits[i-1] 
                            ? 'border-green-500 bg-green-50 text-green-700 focus:border-green-600 focus:ring-2 focus:ring-green-200'
                            : 'border-gray-300 bg-white text-gray-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-200'
                ]"
                ref="inputRefs"
            />
        </div>
        
        <p v-if="helpText" class="text-center text-xs text-gray-500 mt-2">
            {{ helpText }}
        </p>
        
        <div v-if="showError && error" class="mt-3 p-2 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-center justify-center">
                <svg class="h-4 w-4 text-red-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-red-700 text-xs">{{ error }}</span>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'OtpInput',
    props: {
        length: {
            type: Number,
            default: 6
        },
        label: {
            type: String,
            default: 'Enter Verification Code'
        },
        helpText: {
            type: String,
            default: 'Enter the 6-digit code from your email'
        },
        placeholderChar: {
            type: String,
            default: '○'
        },
        disabled: {
            type: Boolean,
            default: false
        },
        modelValue: {
            type: String,
            default: ''
        },
        showError: {
            type: Boolean,
            default: false
        },
        error: {
            type: String,
            default: ''
        }
    },
    emits: ['update:modelValue', 'complete', 'input'],
    data() {
        return {
            digits: Array(this.length).fill('')
        };
    },
    watch: {
        modelValue: {
            immediate: true,
            handler(newValue) {
                if (newValue && newValue.length === this.length) {
                    this.digits = newValue.split('');
                }
            }
        },
        digits: {
            deep: true,
            handler(newDigits) {
                const otp = newDigits.join('');
                this.$emit('update:modelValue', otp);
                this.$emit('input', otp);
                
                // Emit complete event when all digits are filled
                if (otp.length === this.length) {
                    this.$emit('complete', otp);
                }
            }
        }
    },
    methods: {
        onInput(index, event) {
            const value = event.target.value;
            
            // Only allow numbers
            if (!/^\d*$/.test(value)) {
                event.target.value = '';
                this.digits[index-1] = '';
                return;
            }
            
            // If a number is entered, move to next input
            if (value && index < this.length) {
                this.$nextTick(() => {
                    this.focusInput(index);
                });
            }
        },
        
        onKeyDown(index, event) {
            // Handle backspace
            if (event.key === 'Backspace') {
                if (!event.target.value && index > 1) {
                    // If current input is empty, move to previous input
                    this.$nextTick(() => {
                        this.focusInput(index - 2);
                    });
                } else if (event.target.value) {
                    // If current input has value, clear it
                    this.digits[index-1] = '';
                }
            }
            
            // Handle arrow keys
            if (event.key === 'ArrowLeft' && index > 1) {
                event.preventDefault();
                this.focusInput(index - 2);
            }
            
            if (event.key === 'ArrowRight' && index < this.length) {
                event.preventDefault();
                this.focusInput(index);
            }
        },
        
        onPaste(event) {
            event.preventDefault();
            const pasteData = event.clipboardData.getData('text').trim();
            
            // Only allow numbers
            if (!/^\d+$/.test(pasteData)) {
                return;
            }
            
            // Take only the first 'length' characters
            const numbers = pasteData.substring(0, this.length).split('');
            
            // Fill the inputs
            for (let i = 0; i < Math.min(numbers.length, this.length); i++) {
                this.digits[i] = numbers[i];
            }
            
            // Focus on the last filled input or first empty input
            const lastFilledIndex = numbers.length - 1;
            if (lastFilledIndex < this.length - 1) {
                this.focusInput(lastFilledIndex + 1);
            } else {
                this.focusInput(this.length - 1);
            }
        },
        
        onFocus(index) {
            // Select the text when input is focused
            this.$nextTick(() => {
                const input = this.$refs.inputRefs[index-1];
                if (input) {
                    input.select();
                }
            });
        },
        
        focusInput(index) {
            if (this.$refs.inputRefs && this.$refs.inputRefs[index]) {
                this.$refs.inputRefs[index].focus();
            }
        },
        
        // Public method to clear OTP
        clear() {
            this.digits = Array(this.length).fill('');
            if (this.$refs.inputRefs && this.$refs.inputRefs[0]) {
                this.$refs.inputRefs[0].focus();
            }
        },
        
        // Public method to set OTP
        setValue(value) {
            if (value && value.length === this.length) {
                this.digits = value.split('');
            }
        }
    },
    mounted() {
        // Auto-focus first input on mount
        this.$nextTick(() => {
            if (this.$refs.inputRefs && this.$refs.inputRefs[0]) {
                this.$refs.inputRefs[0].focus();
            }
        });
    }
};
</script>

<style scoped>
.otp-input input {
    transition: all 0.2s ease-in-out;
}

.otp-input input:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Custom styles for placeholder */
input::placeholder {
    color: #d1d5db;
    font-weight: normal;
}
</style>