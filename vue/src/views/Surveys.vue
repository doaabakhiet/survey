<template>
  <PageComponent>
    <template v-slot:header>
      <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">
          All Surveys
        </h1>
        <router-link
          :to="{ name: 'SurveyCreate' }"
          class="py-2 px-3 text-white bg-emerald-500 rounded-md hover:bg-emerald-600"
        >
          + Add New Survey
        </router-link>
      </div>
    </template>
    <!-- {{surveyLoading }} -->
    <div v-if="surveys.loading" class="flex justify-center">Loading ...</div>
    <div v-else>
      <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 md:grid-cols-3">
        <SurveyListItem
          v-for="(survey, index) in surveys.data.data"
          :key="survey.id"
          :survey="survey"
          class="opacity animate-fade-in-down"
          :style="{ animationDelay: `${index * 0.1}s` }"
          @delete="deleteSurvey(survey)"
        ></SurveyListItem>
      </div>
      <div class="flex justify-center mt-5">
        <nav
          class="relative z-0 inline-flex justify-center rounded-md shadow-sm -space-x-px"
          aria-label="Pagination"
        >
        <a
            v-for="(link, i) of surveys.links"
            :key="i"
            :disabled="!link.url"
            href="#"
            @click="getForPage($event, link)"
            aria-current="page"
            class="relative inline-flex items-center px-4 py-2 border text-sm font-medium whitespace-nowrap"
            :class="[
              link.active
                ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
              i === 0 ? 'rounded-l-md bg-gray-100 text-gray-700' : '',
              i === surveys.links.length - 1 ? 'rounded-r-md' : '',
            ]"
            v-html="link.label"
          >
          </a>
        </nav>
      </div>
    </div>
  </PageComponent>
</template>
<script setup>
import { computed } from "vue";
import PageComponent from "../components/PageComponent.vue";
import SurveyListItem from "../components/SurveyListItem.vue";
import store from "../store";
store.dispatch("getSurveys");
const surveys = computed(() => store.state.surveys);

function deleteSurvey(survey) {
  if (confirm("Are u sure u want to delete this survey")) {
    store.dispatch("deleteSurvey", survey.id).then(() => {
      store.dispatch("getSurveys");
    });
  }
}
function getForPage($event, link){
  $event.preventDefault();
  if(!link.url || link.active){
    return;
  }
  store.dispatch('getSurveys',{url:link.url});
}
</script>
<style></style>
