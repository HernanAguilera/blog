import { createConfigForNuxt } from '@nuxt/eslint'

export default createConfigForNuxt({
  features: {
    typescript: true,
    stylistic: {
      indent: 2,
      quotes: 'single',
      semi: true,
    }
  }
}).append({
  rules: {
    '@typescript-eslint/no-unused-vars': 'warn',
    '@typescript-eslint/no-explicit-any': 'warn',
    'vue/multi-word-component-names': 'off',
    'vue/no-multiple-template-root': 'off',
  }
})