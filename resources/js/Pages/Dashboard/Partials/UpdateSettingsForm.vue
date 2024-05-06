<template>
    <jet-form-section :full="true" @submitted="updateSettings">
        <template #title>Site Settings</template>

        <template #description>
            Settings that apply to the entire site.
        </template>

        <template #form>
            <div class="col-span-2">
                <jet-label for="title" value="Title" />
                <info-text>
                    This is the name of the site to your users. It is used at
                    the top of every page, and in emails that are sent from the
                    site. It is also used when sharing pages to social media.
                </info-text>
                <jet-input
                    id="title"
                    v-model="form.title"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="title"
                />
                <jet-input-error :message="form.errors.title" class="mt-2" />
            </div>

            <div class="col-span-6">
                <jet-label for="description" value="Description" />
                <info-text>
                    This is the text content on the home page. It is also used
                    when sharing pages to social media.
                </info-text>
                <wysiwyg v-model="form.description" />
                <jet-input-error
                    :message="form.errors.description"
                    class="mt-2"
                />
            </div>

            <div class="col-span-6 md:col-span-2">
                <jet-label for="registration" value="Registration Enabled" />
                <info-text
                    >Turning this off removes the ability for anyone to register
                    to the site, even if they were sent an invite.</info-text
                >
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

            <div v-if="form.registration" class="col-span-6 md:col-span-2">
                <jet-label
                    for="registration_secret"
                    value="Registration Secret"
                />
                <info-text
                    >New users will need to enter this secret in the
                    registration form. This makes sure only people you invite
                    can register.</info-text
                >
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
import Checkbox from "@/Base/Checkbox.vue";
import Wysiwyg from "@/Base/Wysiwyg.vue";
import InfoText from "@/Base/InfoText.vue";

export default defineComponent({
    components: {
        InfoText,
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
