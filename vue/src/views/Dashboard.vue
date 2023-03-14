<template>
  <PageComponent>
    <div v-if="loading" class="flex justify-center">Loading...</div>
    <div
      v-else
      class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 text-gray-700"
    >
      <div
        class="bg-white shadow-md p-3 text-center flex flex-col order-1 lg:order-2 animate-fade-in-down"
        style="animation-delay: 0.1s"
      >
        <h3 class="text-2xl font-semibold">Total Surveys</h3>
        <div
          class="text-8xl font-semibold flex-1 flex items-center justify-center"
        >
          {{ data.totalSurveys }}
        </div>
      </div>
      <div
        class="bg-white shadow-md p-3 text-center flex flex-col order-2 lg:order-4 animate-fade-in-down"
        style="animation-delay: 0.1s"
      >
        <h3 class="text-2xl font-semibold">Total Answers</h3>
        <div
          class="text-8xl font-semibold flex-1 flex items-center justify-center"
        >
          {{ data.totalAnswerSurveys }}
        </div>
      </div>

      <div
        class="row-span-2 bg-white shadow-md p-4 order-3 lg:order-1 animate-fade-in-down"
        style="animation-delay: 0.1s"
      >
        <h3 class="text-2xl font-semibold">Total Answers</h3>
        <img :src="data.latestSurveys.image_url" class="w-[240px] mx-auto" />
        <h3 class="font-bold text-xl mb-3">{{ data.latestSurveys.title }}</h3>
        <div class="flex justify-between text-sm mb-1">
          <div>Upload Date:</div>
          <div>{{ data.latestSurveys.created_at }}</div>
        </div>
        <div class="flex justify-between text-sm mb-1">
          <div>Expire Date:</div>
          <div>{{ data.latestSurveys.expire_date }}</div>
        </div>
        <div class="flex justify-between text-sm mb-1">
          <div>Status:</div>
          <div>{{ data.latestSurveys.status?'Active':'Draft' }}</div>
        </div>
        <div class="flex justify-between text-sm mb-1">
          <div>Questions:</div>
          <div>{{ data.latestSurveys.questions }}</div>
        </div>
        <div class="flex justify-between text-sm mb-1">
          <div>Answers:</div>
          <div>{{ data.latestSurveys.answers }}</div>
        </div>
        <div class="flex justify-between text-sm mb-1">
          <router-link
            :to="{ name: 'SurveyView', params: { id: data.latestSurveys.id } }"
            class=""
            >Edit</router-link
          >
          <router-link
            :to="{ name: 'SurveyView', params: { id: data.latestSurveys.id } }"
            class=""
            >view</router-link
          >
          <router-link
            :to="{ name: 'SurveyView', params: { id: data.latestSurveys.id } }"
            class=""
            >delete</router-link
          >
        </div>
      </div>
      <div
        class="row-span-2 bg-white shadow-md p-3 order-4 lg:order-3 animate-fade-in-down"
        style="animation-delay: 0.1s"
      >
        <div class="flex justify-between items-center text-sm mb-1">
          <h3 class="text-2xl font-semibold">Latest Answers</h3>
          <a
            href="javascript:void(0)"
            class="text-sm text-blue-500 hover:decoration-blue-500"
          >
            view all</a
          >
        </div>
        <a
          href="#"
          v-for="answer in data.latestAnswerSurveys"
          :key="answer.id"
          class="block p-2 hover:bg-gray-100/90"
        >
          <div class="font-semibold">{{ answer.survey.title }}</div>
          <small
            >Answer Made At:
            <i class="font-semibold">{{ answer.end_date }}</i></small
          >
        </a>
      </div>
    </div>
  </PageComponent>
</template>
<script setup>
import { useStore } from "vuex";
import PageComponent from "../components/PageComponent.vue";
import { computed } from "vue";
const store = useStore();
const loading = computed(() => store.state.dashboard.loading);
const data = computed(() => store.state.dashboard.data);

store.dispatch("getDashboardData");
</script>
