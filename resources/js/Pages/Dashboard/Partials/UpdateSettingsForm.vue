<template>
    <JetFormSection :full="true" @submitted="updateSettings">
        <template #title>Site Settings</template>

        <template #description>
            Settings that apply to the entire site.
        </template>

        <template #form>
            <div class="col-span-2">
                <JetLabel for="title" value="Title" />
                <InfoText>
                    This is the name of the site to your users. It is used at
                    the top of every page, and in emails that are sent from the
                    site. It is also used when sharing pages to social media.
                </InfoText>
                <JetInput
                    id="title"
                    v-model="form.title"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="title"
                />
                <JetInputError :message="form.errors.title" class="mt-2" />
            </div>

            <div class="col-span-6">
                <JetLabel for="description" value="Description" />
                <InfoText>
                    This is the text content on the home page. It is also used
                    when sharing pages to social media.
                </InfoText>
                <wysiwyg v-model="form.description" />
                <JetInputError
                    :message="form.errors.description"
                    class="mt-2"
                />
            </div>

            <div class="col-span-6 md:col-span-2">
                <JetLabel for="registration" value="Registration Enabled" />
                <InfoText
                    >Turning this off removes the ability for anyone to register
                    to the site, even if they were sent an invite.</InfoText
                >
                <Checkbox
                    id="registration"
                    v-model:checked="form.registration"
                    name="registration"
                />
                <JetInputError
                    :message="form.errors.registration"
                    class="mt-2"
                />
            </div>

            <div v-if="form.registration" class="col-span-6 md:col-span-2">
                <JetLabel
                    for="registration_secret"
                    value="Registration Secret"
                />
                <InfoText
                    >New users will need to enter this secret in the
                    registration form. This makes sure only people you invite
                    can register.</InfoText
                >
                <JetInput
                    id="registration_secret"
                    v-model="form.registration_secret"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="registration_secret"
                />
                <JetInputError
                    :message="form.errors.registration_secret"
                    class="mt-2"
                />
            </div>
        </template>

        <template #actions>
            <BaseButton
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing || !form.isDirty"
            >
                Save
            </BaseButton>

            <JetActionMessage :on="form.recentlySuccessful" class="ml-3">
                Saved.
            </JetActionMessage>
        </template>
    </JetFormSection>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import JetFormSection from "@/Base/FormSection.vue";
import JetInput from "@/Base/Input.vue";
import JetInputError from "@/Base/InputError.vue";
import JetLabel from "@/Base/Label.vue";
import JetActionMessage from "@/Base/ActionMessage.vue";
import Checkbox from "@/Base/Checkbox.vue";
import Wysiwyg from "@/Base/Wysiwyg.vue";
import InfoText from "@/Base/InfoText.vue";

const props = defineProps({
    settings: Object,
});

const form = useForm({
    _method: "PUT",
    registration: Boolean(props.settings.registration),
    registration_secret: props.settings.registration_secret,
    description: props.settings.description,
    title: props.settings.title,
});

const updateSettings = () => {
    form.post(route("site-settings.update", props.settings), {
        errorBag: "updateSettings",
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>
