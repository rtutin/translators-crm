const { createApp, defineComponent, ref, computed } = Vue;

const TranslatorRow = defineComponent({
    name: 'TranslatorRow',
    props: {
        translator: { type: Object, required: true },
    },
    template: `
        <tr>
            <td>{{ translator.id }}</td>
            <td>{{ translator.full_name }}</td>
            <td><span class="badge bg-info text-dark">{{ translator.language_pair }}</span></td>
            <td>{{ translator.schedule_label }}</td>
            <td>
                <span :class="translator.is_available ? 'badge bg-success' : 'badge bg-secondary'">
                    {{ translator.is_available ? 'Свободен' : 'Занят' }}
                </span>
            </td>
        </tr>
    `,
});

const TranslatorsList = defineComponent({
    name: 'TranslatorsList',
    components: { TranslatorRow },
    props: {
        initialTranslators: { type: Array, default: () => [] },
    },
    setup(props) {
        const translators = ref(props.initialTranslators);
        const search = ref('');

        const filtered = computed(() => {
            if (!search.value.trim()) return translators.value;
            const q = search.value.toLowerCase();
            return translators.value.filter(t =>
                t.full_name.toLowerCase().includes(q) ||
                t.language_pair.toLowerCase().includes(q)
            );
        });

        return { translators, search, filtered };
    },
    template: `
        <div>
            <div class="mb-3">
                <input
                    v-model="search"
                    type="text"
                    class="form-control"
                    placeholder="Поиск по имени или языковой паре..."
                    style="max-width: 380px;"
                />
            </div>

            <div v-if="filtered.length === 0" class="alert alert-warning">
                Нет свободных переводчиков
            </div>

            <table v-else class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Имя</th>
                        <th>Языковая пара</th>
                        <th>График</th>
                        <th>Статус</th>
                    </tr>
                </thead>
                <tbody>
                    <translator-row
                        v-for="t in filtered"
                        :key="t.id"
                        :translator="t"
                    />
                </tbody>
            </table>

            <p class="text-muted">Найдено: {{ filtered.length }}</p>
        </div>
    `,
});

const app = createApp({
    components: { TranslatorsList },
    setup() {
        return {
            initialTranslators: window.__TRANSLATORS__ || [],
        };
    },
    template: `<translators-list :initial-translators="initialTranslators" />`,
});

app.mount('#translators-app');
