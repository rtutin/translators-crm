const { createApp, defineComponent, ref, reactive, computed } = Vue;

function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.content : '';
}

async function apiFetch(url, method = 'GET', body = null) {
    const options = {
        method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': getCsrfToken(),
        },
    };
    if (body) options.body = JSON.stringify(body);
    const res = await fetch(url, options);
    return res.json();
}

const TranslatorForm = defineComponent({
    name: 'TranslatorForm',
    props: {
        initial: { type: Object, default: null },
    },
    emits: ['saved', 'cancel'],
    setup(props, { emit }) {
        const form = reactive({
            id:            props.initial?.id            ?? null,
            full_name:     props.initial?.full_name     ?? '',
            language_pair: props.initial?.language_pair ?? '',
            work_schedule: props.initial?.work_schedule ?? 'weekday',
            is_available:  props.initial?.is_available  ?? true,
        });
        const errors = ref({});
        const saving = ref(false);

        async function submit() {
            saving.value = true;
            errors.value = {};
            const data = await apiFetch(window.__SAVE_URL__, 'POST', form);
            saving.value = false;
            if (data.success) {
                emit('saved', data.model);
            } else {
                errors.value = data.errors || {};
            }
        }

        return { form, errors, saving, submit };
    },
    template: `
        <div class="card mb-4">
            <div class="card-header">{{ form.id ? 'Редактировать переводчика' : 'Добавить переводчика' }}</div>
            <div class="card-body">
                <div class="mb-2">
                    <label class="form-label">Имя</label>
                    <input v-model="form.full_name" class="form-control" :class="{'is-invalid': errors.full_name}" />
                    <div v-if="errors.full_name" class="invalid-feedback">{{ errors.full_name.join(', ') }}</div>
                </div>
                <div class="mb-2">
                    <label class="form-label">Языковая пара</label>
                    <input v-model="form.language_pair" class="form-control" placeholder="EN-RU" :class="{'is-invalid': errors.language_pair}" />
                    <div v-if="errors.language_pair" class="invalid-feedback">{{ errors.language_pair.join(', ') }}</div>
                </div>
                <div class="mb-2">
                    <label class="form-label">График работы</label>
                    <select v-model="form.work_schedule" class="form-select">
                        <option value="weekday">Будни (Пн–Пт)</option>
                        <option value="weekend">Выходные (Сб–Вс)</option>
                        <option value="both">Будни и выходные</option>
                    </select>
                </div>
                <div class="mb-3 form-check">
                    <input v-model="form.is_available" type="checkbox" class="form-check-input" id="isAvailableCheck" />
                    <label class="form-check-label" for="isAvailableCheck">Свободен прямо сейчас</label>
                </div>
                <button @click="submit" class="btn btn-success" :disabled="saving">
                    {{ saving ? 'Сохраняю...' : 'Сохранить' }}
                </button>
                <button @click="$emit('cancel')" class="btn btn-secondary ms-2">Отмена</button>
            </div>
        </div>
    `,
});

const TranslatorRow = defineComponent({
    name: 'TranslatorRow',
    props: {
        translator: { type: Object, required: true },
    },
    emits: ['edit', 'deleted'],
    setup(props, { emit }) {
        const deleting = ref(false);

        async function remove() {
            if (!confirm(`Удалить «${props.translator.full_name}»?`)) return;
            deleting.value = true;
            await apiFetch(window.__DELETE_URL__ + '&id=' + props.translator.id, 'POST');
            emit('deleted', props.translator.id);
        }

        return { deleting, remove };
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
            <td>
                <button @click="$emit('edit', translator)" class="btn btn-sm btn-outline-primary me-1">✏️</button>
                <button @click="remove" class="btn btn-sm btn-outline-danger" :disabled="deleting">🗑️</button>
            </td>
        </tr>
    `,
});

const PaginationBar = defineComponent({
    name: 'PaginationBar',
    props: {
        page:       { type: Number, required: true },
        pageCount:  { type: Number, required: true },
        totalCount: { type: Number, required: true },
    },
    emits: ['change'],
    template: `
        <nav v-if="pageCount > 1" class="mt-3">
            <ul class="pagination pagination-sm">
                <li class="page-item" :class="{ disabled: page === 1 }">
                    <button class="page-link" @click="$emit('change', page - 1)">‹</button>
                </li>
                <li
                    v-for="p in pageCount" :key="p"
                    class="page-item" :class="{ active: p === page }"
                >
                    <button class="page-link" @click="$emit('change', p)">{{ p }}</button>
                </li>
                <li class="page-item" :class="{ disabled: page === pageCount }">
                    <button class="page-link" @click="$emit('change', page + 1)">›</button>
                </li>
            </ul>
            <small class="text-muted">Всего: {{ totalCount }}</small>
        </nav>
    `,
});

const SortableHeader = defineComponent({
    name: 'SortableHeader',
    props: {
        field:       { type: String, required: true },
        label:       { type: String, required: true },
        currentSort: { type: String, default: '' },
    },
    emits: ['sort'],
    setup(props, { emit }) {
        const isAsc  = computed(() => props.currentSort === props.field);
        const isDesc = computed(() => props.currentSort === '-' + props.field);

        function toggle() {
            if (isAsc.value)       emit('sort', '-' + props.field);
            else if (isDesc.value) emit('sort', '');
            else                   emit('sort', props.field);
        }

        const icon = computed(() => {
            if (isAsc.value)  return '↑';
            if (isDesc.value) return '↓';
            return ' ⇅';
        });

        return { toggle, icon };
    },
    template: `
        <th @click="toggle" style="cursor:pointer;user-select:none;">
            {{ label }} {{ icon }}
        </th>
    `,
});

const App = defineComponent({
    name: 'TranslatorsApp',
    components: { TranslatorForm, TranslatorRow, PaginationBar, SortableHeader },
    setup() {
        const translators = ref(window.__TRANSLATORS__ || []);
        const pagination  = reactive(window.__PAGINATION__ || { page: 1, pageCount: 1, pageSize: 20, totalCount: 0 });
        const showForm    = ref(false);
        const editTarget  = ref(null);
        const currentSort = ref(window.__SORT__ || '');

        const currentParams = new URLSearchParams(window.location.search);
        const searchQuery = ref(currentParams.get('TranslatorSearch[q]') || '');

        function applySearch() {
            const url = new URL(window.location.href);
            url.searchParams.delete('page');
            if (searchQuery.value.trim()) {
                url.searchParams.set('TranslatorSearch[q]', searchQuery.value.trim());
            } else {
                url.searchParams.delete('TranslatorSearch[q]');
            }
            window.location.href = url.toString();
        }

        function clearSearch() {
            searchQuery.value = '';
            applySearch();
        }

        function applySort(sortValue) {
            const url = new URL(window.location.href);
            url.searchParams.delete('page');
            if (sortValue) {
                url.searchParams.set('sort', sortValue);
            } else {
                url.searchParams.delete('sort');
            }
            window.location.href = url.toString();
        }

        function openCreate() { editTarget.value = null; showForm.value = true; }
        function openEdit(t)  { editTarget.value = t;    showForm.value = true; }
        function closeForm()  { showForm.value = false; editTarget.value = null; }

        function onSaved(model) {
            const idx = translators.value.findIndex(t => t.id === model.id);
            if (idx !== -1) {
                translators.value.splice(idx, 1, model);
            } else {
                translators.value.unshift(model);
                pagination.totalCount++;
            }
            closeForm();
        }

        function onDeleted(id) {
            translators.value = translators.value.filter(t => t.id !== id);
            pagination.totalCount--;
        }

        function onPageChange(p) {
            const url = new URL(window.location.href);
            url.searchParams.set('page', p);
            window.location.href = url.toString();
        }

        return {
            translators, pagination, currentSort,
            searchQuery, applySearch, clearSearch, applySort,
            showForm, editTarget,
            openCreate, openEdit, closeForm,
            onSaved, onDeleted, onPageChange,
        };
    },
    template: `
        <div>
            <div class="row mb-3 g-2 align-items-center">
                <div class="col-auto">
                    <button @click="openCreate" class="btn btn-primary">+ Добавить переводчика</button>
                </div>
                <div class="col">
                    <div class="input-group" style="max-width:400px">
                        <input
                            v-model="searchQuery"
                            type="text"
                            class="form-control"
                            placeholder="Поиск по имени или языковой паре..."
                            @keyup.enter="applySearch"
                        />
                        <button class="btn btn-outline-secondary" @click="applySearch">🔍</button>
                        <button v-if="searchQuery" class="btn btn-outline-danger" @click="clearSearch">✕</button>
                    </div>
                </div>
            </div>

            <translator-form
                v-if="showForm"
                :initial="editTarget"
                @saved="onSaved"
                @cancel="closeForm"
            />

            <div v-if="translators.length === 0" class="alert alert-warning">
                Нет переводчиков по заданному фильтру.
            </div>

            <table v-else class="table table-striped table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <sortable-header field="id"            label="#"              :current-sort="currentSort" @sort="applySort" />
                        <sortable-header field="full_name"     label="Имя"            :current-sort="currentSort" @sort="applySort" />
                        <sortable-header field="language_pair" label="Языковая пара"  :current-sort="currentSort" @sort="applySort" />
                        <sortable-header field="work_schedule" label="График"         :current-sort="currentSort" @sort="applySort" />
                        <sortable-header field="is_available"  label="Статус"         :current-sort="currentSort" @sort="applySort" />
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <translator-row
                        v-for="t in translators"
                        :key="t.id"
                        :translator="t"
                        @edit="openEdit"
                        @deleted="onDeleted"
                    />
                </tbody>
            </table>

            <pagination-bar
                :page="pagination.page"
                :page-count="pagination.pageCount"
                :total-count="pagination.totalCount"
                @change="onPageChange"
            />
        </div>
    `,
});

createApp(App).mount('#translators-app');
