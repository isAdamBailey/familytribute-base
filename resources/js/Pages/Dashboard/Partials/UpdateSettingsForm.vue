<template>
    <jet-form-section :full="true" @submitted="updateSettings">
        <template #title>Site Settings</template>

        <template #description>
            Settings that apply to the entire site.
        </template>

        <template #form>
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="title" value="Title" />
                <div class="flex items-center text-sm text-indigo-600">
                    <i class="ri-information-line mr-1"></i>
                    This is what shows at the top of the page next to the menu.
                </div>
                <jet-input
                    id="title"
                    v-model="form.title"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="title"
                />
                <jet-input-error :message="form.errors.title" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <jet-label for="description" value="Description" />
                <div class="flex items-center text-sm text-indigo-600">
                    <i class="ri-information-line mr-1"></i>
                    This is the text content on the home page.
                </div>
                <wysiwyg v-model="form.description" />
                <jet-input-error
                    :message="form.errors.description"
                    class="mt-2"
                />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <jet-label for="registration" value="Registration Enabled" />
                <div class="flex items-center text-sm text-indigo-600">
                    <i class="ri-information-line mr-1"></i>
                    Turning this off removes the ability to register to the site
                </div>
                <checkbox
                    id="registration"
                    v-model:checked="form.registration"
                    name="registration"
                />
                <jet-input-error
                    :message="form.errors.registration"
                    class="mt-2"
                />
            </div>

            <div v-if="form.registration" class="col-span-6 sm:col-span-4">
                <jet-label
                    for="registration_secret"
                    value="Registration Secret"
                />
                <div class="flex items-center text-sm text-indigo-600">
                    <i class="ri-information-line mr-1"></i>
                    New users will need to enter this in order to register to
                    the site
                </div>
                <jet-input
                    id="registration_secret"
                    v-model="form.registration_secret"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="registration_secret"
                />
                <jet-input-error
                    :message="form.errors.registration_secret"
                    class="mt-2"
                />
            </div>
        </template>

        <template #actions>
            <base-button
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing || !form.isDirty"
            >
                Save
            </base-button>

            <jet-action-message :on="form.recentlySuccessful" class="ml-3">
                Saved.
            </jet-action-message>
        </template>
    </jet-form-section>
</template>

<script>
import { defineComponent } from "vue";
import JetFormSection from "@/Base/FormSection.vue";
import JetInput from "@/Base/Input.vue";
import JetInputError from "@/Base/InputError.vue";
import JetLabel from "@/Base/Label.vue";
import JetActionMessage from "@/Base/ActionMessage.vue";
import Checkbox from "@/Base/Checkbox";
import Wysiwyg from "@/Base/Wysiwyg";

export default defineComponent({
    components: {
        Wysiwyg,
        Checkbox,
        JetActionMessage,
        JetFormSection,
        JetInput,
        JetInputError,
        JetLabel,
    },

    props: {
        settings: Object,
    },

    data() {
        return {
            form: this.$inertia.form({
                _method: "PUT",
                registration: Boolean(this.settings.registration),
                registration_secret: this.settings.registration_secret,
                description: this.settings.description,
                title: this.settings.title,
            }),
        };
    },

    methods: {
        updateSettings() {
            this.form.post(route("site-settings.update", this.settings), {
                errorBag: "updateSettings",
                preserveScroll: true,
                preserveState: false,
                onSuccess: () => {
                    this.form.reset();
                },
            });
        },
    },
});
</script>
