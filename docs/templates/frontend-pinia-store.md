# Frontend - Pinia Store Template

```typescript
// Template: interface/stores/{entityName}.ts
import { defineStore } from 'pinia';
import { ref, computed, readonly } from 'vue';
import { container } from '~/shared/container/container';
import type { {UseCase} } from '~/application/use-cases/{domain}/{UseCase}';
import type { {EntityName} } from '~/domain/entities/{EntityName}';

export const use{EntityName}Store = defineStore('{entityName}', () => {
    // Use cases
    const {useCase} = container().get<{UseCase}>('{UseCase}');

    // State
    const {items} = ref<{EntityName}[]>([]);
    const current{EntityName} = ref<{EntityName} | null>(null);
    const isLoading = ref(false);

    // Actions
    const load{Items} = async (params?: {LoadParams}) => {
        isLoading.value = true;
        try {
            const result = await {useCase}.execute(params || {});
            {items}.value = result.{items};
        } finally {
            isLoading.value = false;
        }
    };

    // ... other actions

    return {
        // State (readonly)
        {items}: readonly({items}),
        current{EntityName}: readonly(current{EntityName}),
        isLoading: readonly(isLoading),
        
        // Actions
        load{Items},
        // ... other actions
    };
});
```
