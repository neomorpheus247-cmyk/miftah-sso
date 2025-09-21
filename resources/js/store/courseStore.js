import { defineStore } from 'pinia';
import axios from 'axios';

export const useCourseStore = defineStore('course', {
  state: () => ({
    courses: [],
    currentCourse: null,
    loading: false,
    error: null
  }),

  actions: {
    async fetchCourses() {
      this.loading = true;
      try {
        const response = await axios.get('/api/courses');
        this.courses = response.data.data;
        this.error = null;
      } catch (err) {
        this.error = err.response?.data?.message || 'Error fetching courses';
      } finally {
        this.loading = false;
      }
    },

    async fetchCourse(id) {
      this.loading = true;
      try {
        const response = await axios.get(`/api/courses/${id}`);
        this.currentCourse = response.data.data;
        this.error = null;
      } catch (err) {
        this.error = err.response?.data?.message || 'Error fetching course';
      } finally {
        this.loading = false;
      }
    },

    async createCourse(courseData) {
      this.loading = true;
      try {
        const response = await axios.post('/api/courses', courseData);
        this.courses.push(response.data.data);
        this.error = null;
        return response.data.data;
      } catch (err) {
        this.error = err.response?.data?.message || 'Error creating course';
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async updateCourse(id, courseData) {
      this.loading = true;
      try {
        const response = await axios.put(`/api/courses/${id}`, courseData);
        const index = this.courses.findIndex(c => c.id === id);
        if (index !== -1) {
          this.courses[index] = response.data.data;
        }
        this.error = null;
        return response.data.data;
      } catch (err) {
        this.error = err.response?.data?.message || 'Error updating course';
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async deleteCourse(id) {
      this.loading = true;
      try {
        await axios.delete(`/api/courses/${id}`);
        this.courses = this.courses.filter(c => c.id !== id);
        this.error = null;
      } catch (err) {
        this.error = err.response?.data?.message || 'Error deleting course';
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async enrollInCourse(courseId) {
      this.loading = true;
      try {
        await axios.post(`/api/courses/${courseId}/enroll`);
        await this.fetchCourse(courseId);
        this.error = null;
      } catch (err) {
        this.error = err.response?.data?.message || 'Error enrolling in course';
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async unenrollFromCourse(courseId) {
      this.loading = true;
      try {
        await axios.post(`/api/courses/${courseId}/unenroll`);
        await this.fetchCourse(courseId);
        this.error = null;
      } catch (err) {
        this.error = err.response?.data?.message || 'Error unenrolling from course';
        throw err;
      } finally {
        this.loading = false;
      }
    }
  }
});