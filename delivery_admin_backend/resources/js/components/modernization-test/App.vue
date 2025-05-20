<template>
    <div class="modernization-test">
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">
                            Toast Notifications (Toastr Replacement)
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="btn-group">
                            <button
                                @click="showSuccessToast"
                                class="btn btn-success mr-2"
                            >
                                Success Toast
                            </button>
                            <button
                                @click="showErrorToast"
                                class="btn btn-danger mr-2"
                            >
                                Error Toast
                            </button>
                            <button
                                @click="showInfoToast"
                                class="btn btn-info mr-2"
                            >
                                Info Toast
                            </button>
                            <button
                                @click="showWarningToast"
                                class="btn btn-warning"
                            >
                                Warning Toast
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">
                            Date Picker (Moment.js Replacement)
                        </h5>
                    </div>
                    <div class="card-body">
                        <DatePicker
                            v-model="selectedDate"
                            placeholder="Select a date"
                            @change="onDateChange"
                        />

                        <div class="mt-3">
                            <p>
                                <strong>Selected Date:</strong>
                                {{ selectedDate }}
                            </p>
                            <p>
                                <strong>Formatted Date:</strong>
                                {{ formattedDate }}
                            </p>
                            <p>
                                <strong>Relative Time:</strong>
                                {{ relativeTime }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">
                            Enhanced Select (Select2 Replacement)
                        </h5>
                    </div>
                    <div class="card-body">
                        <EnhancedSelect
                            v-model="selectedOption"
                            :options="selectOptions"
                            placeholder="Select an option"
                            @change="onSelectChange"
                        />

                        <div class="mt-3">
                            <p>
                                <strong>Selected Option:</strong>
                                {{ selectedOption }}
                            </p>
                        </div>

                        <div class="mt-4">
                            <h6>Multiple Select</h6>
                            <EnhancedSelect
                                v-model="selectedOptions"
                                :options="selectOptions"
                                placeholder="Select options"
                                multiple
                                @change="onMultiSelectChange"
                            />

                            <div class="mt-3">
                                <p>
                                    <strong>Selected Options:</strong>
                                    {{ selectedOptions }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">
                            Rich Text Editor (TinyMCE Replacement)
                        </h5>
                    </div>
                    <div class="card-body">
                        <RichTextEditor
                            v-model="editorContent"
                            placeholder="Write something..."
                            @update:modelValue="onEditorChange"
                        />

                        <div class="mt-3">
                            <h6>Editor Content Preview:</h6>
                            <div
                                class="border p-3 rounded"
                                v-html="editorContent"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { useToast } from "../../plugins/toast";
import dayjs from "dayjs";
import relativeTimePlugin from "dayjs/plugin/relativeTime";
import DatePicker from "../../components/common/DatePicker.vue";
import EnhancedSelect from "../../components/common/EnhancedSelect.vue";
import RichTextEditor from "../../components/common/RichTextEditor.vue";

// Setup dayjs plugins
dayjs.extend(relativeTimePlugin);

// Toast notifications
const toast = useToast();

const showSuccessToast = () => {
    toast.success("This is a success message!");
};

const showErrorToast = () => {
    toast.error("This is an error message!");
};

const showInfoToast = () => {
    toast.info("This is an info message!");
};

const showWarningToast = () => {
    toast.warning("This is a warning message!");
};

// Date picker
const selectedDate = ref(dayjs().format("YYYY-MM-DD"));
const formattedDate = computed(() => {
    return dayjs(selectedDate.value).format("MMMM D, YYYY");
});
const relativeTime = computed(() => {
    return dayjs(selectedDate.value).fromNow();
});

const onDateChange = (date) => {
    console.log("Date changed:", date);
};

// Enhanced select
const selectOptions = [
    { label: "Option 1", value: 1 },
    { label: "Option 2", value: 2 },
    { label: "Option 3", value: 3 },
    { label: "Option 4", value: 4 },
    { label: "Option 5", value: 5 },
];
const selectedOption = ref(null);
const selectedOptions = ref([]);

const onSelectChange = (value) => {
    console.log("Select changed:", value);
};

const onMultiSelectChange = (values) => {
    console.log("Multi-select changed:", values);
};

// Rich text editor
const editorContent = ref(
    "<p>This is some <strong>rich</strong> <em>text</em> content.</p>"
);

const onEditorChange = (content) => {
    console.log("Editor content changed:", content);
};
</script>

<style scoped>
.modernization-test {
    padding: 1rem;
}

.btn-group .btn {
    margin-right: 0.5rem;
}
</style>
