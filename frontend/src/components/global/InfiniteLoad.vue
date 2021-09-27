<template>
  <div class="infinite-loading-container">
    <div v-show="isLoading">
      <itw-loader></itw-loader>
    </div>
    <div class="infinite-status-prompt" v-show="isNoResults">
      <slot name="no-results">No results :(</slot>
    </div>
    <div class="infinite-status-prompt" v-show="isNoMore">
      <slot name="no-more">No more data :)</slot>
    </div>
  </div>
</template>
<script>
const LOOP_CHECK_TIMEOUT = 1000;
const LOOP_CHECK_MAX_CALLS = 10;
import Loader from '@/components/global/Loader'

export default {
  data() {
    return {
      scrollParent: null,
      scrollHandler: null,
      isLoading: false,
      isComplete: false,
      isFirstLoad: true, // save the current loading whether it is the first loading
      debounceTimer: null,
      debounceDuration: 100,
      infiniteLoopChecked: false, // save the status of infinite loop check
      infiniteLoopTimer: null,
      continuousCallTimes: 0,
    };
  },
  computed: {
    isNoResults: {
      cache: false, // disable cache to fix the problem of get slot text delay
      get() {
        const noResultsSlot = this.$slots['no-results'];
        const isBlankNoResultsSlot = (noResultsSlot && noResultsSlot[0].elm && noResultsSlot[0].elm.textContent === '');
        return !this.isLoading && this.isComplete && this.isFirstLoad && !isBlankNoResultsSlot;
      },
    },
    isNoMore: {
      cache: false, // disable cache to fix the problem of get slot text delay
      get() {
        const noMoreSlot = this.$slots['no-more'];
        const isBlankNoMoreSlot = (noMoreSlot && noMoreSlot[0].elm && noMoreSlot[0].elm.textContent === '');
        return !this.isLoading && this.isComplete && !this.isFirstLoad && !isBlankNoMoreSlot;
      },
    },
  },
  components: {
    'itw-loader': Loader
  },
  props: {
    distance: {
      type: Number,
      default: 100,
    },
    onInfinite: Function,
    direction: {
      type: String,
      default: 'bottom',
    },
    forceUseInfiniteWrapper: null,
  },
  mounted() {
    this.scrollParent = this.getScrollParent();

    this.scrollHandler = function scrollHandlerOriginal(ev) {
      if (!this.isLoading) {
        clearTimeout(this.debounceTimer);

        if (ev && ev.constructor === Event) {
          this.debounceTimer = setTimeout(this.attemptLoad, this.debounceDuration);
        } else {
          this.attemptLoad();
        }
      }
    }.bind(this);
    if(this.scrollParent) {

      setTimeout(this.scrollHandler, 1);
      this.scrollParent.addEventListener('scroll', this.scrollHandler);

      this.$on('$InfiniteLoading:loaded', (ev) => {
        this.isFirstLoad = false;

        if (this.isLoading) {
          this.$nextTick(this.attemptLoad.bind(null, true));
        }

        if (!ev || ev.target !== this) {
          // console.warn(WARNINGS.STATE_CHANGER);
        }
      });

      this.$on('$InfiniteLoading:complete', (ev) => {
        this.isLoading = false;
        this.isComplete = true;

        // force re-complation computed properties to fix the problem of get slot text delay
        this.$nextTick(() => {
          this.$forceUpdate();
        });

        this.scrollParent.removeEventListener('scroll', this.scrollHandler);

        if (!ev || ev.target !== this) {
          // console.warn(WARNINGS.STATE_CHANGER);
        }
      });

      this.$on('$InfiniteLoading:reset', () => {
        this.isLoading = false;
        this.isComplete = false;
        this.isFirstLoad = true;
        this.scrollParent.addEventListener('scroll', this.scrollHandler);
        setTimeout(this.scrollHandler, 1);
      });

      if (this.onInfinite) {
        // console.warn(WARNINGS.INFINITE_EVENT);
      }
      this.stateChanger = {
        loaded: () => {
          this.$emit('$InfiniteLoading:loaded', {target: this});
        },
        complete: () => {
          this.$emit('$InfiniteLoading:complete', {target: this});
        },
        reset: () => {
          this.$emit('$InfiniteLoading:reset', {target: this});
        },
      };
    }
  },
  deactivated() {
    this.isLoading = false;
    this.scrollParent.removeEventListener('scroll', this.scrollHandler);
  },
  activated() {
    this.scrollParent.addEventListener('scroll', this.scrollHandler);
  },
  methods: {
    attemptLoad(isContinuousCall) {
      const currentDistance = this.getCurrentDistance();

      if (!this.isComplete && currentDistance <= this.distance &&
          (this.$el.offsetWidth + this.$el.offsetHeight) > 0) {
        this.isLoading = true;

        if (typeof this.onInfinite === 'function') {
          this.onInfinite.call(null, this.stateChanger);
        } else {
          this.$emit('infinite', this.stateChanger);
        }

        if (isContinuousCall && !this.forceUseInfiniteWrapper && !this.infiniteLoopChecked) {
          // check this component whether be in an infinite loop if it is not checked
          // more details: https://github.com/PeachScript/vue-infinite-loading/issues/55#issuecomment-316934169
          this.continuousCallTimes += 1; // save the times of calls

          clearTimeout(this.infiniteLoopTimer);
          this.infiniteLoopTimer = setTimeout(() => {
            this.infiniteLoopChecked = true;
          }, LOOP_CHECK_TIMEOUT);

          // throw warning if the times of continuous calls large than the maximum times
          if (this.continuousCallTimes > LOOP_CHECK_MAX_CALLS) {
            this.infiniteLoopChecked = true;
          }
        }
      } else {
        this.isLoading = false;
      }
    },
    getCurrentDistance() {
      let distance;

      if (this.direction === 'top') {
        distance = isNaN(this.scrollParent.scrollTop) ?
            this.scrollParent.pageYOffset :
            this.scrollParent.scrollTop;
      } else {
        const infiniteElmOffsetTopFromBottom = this.$el.getBoundingClientRect().top;
        const scrollElmOffsetTopFromBottom = this.scrollParent === window ?
            window.innerHeight :
            this.scrollParent.getBoundingClientRect().bottom;

        distance = infiniteElmOffsetTopFromBottom - scrollElmOffsetTopFromBottom;
      }

      return distance;
    },
    getScrollParent(elm = this.$el) {
      let result;
      if(!elm) return
      if (elm.tagName === 'BODY') {
        result = window;
      } else if (!this.forceUseInfiniteWrapper && ['scroll', 'auto'].indexOf(getComputedStyle(elm).overflowY) > -1) {
        result = elm;
      } else if (elm.hasAttribute('infinite-wrapper') || elm.hasAttribute('data-infinite-wrapper')) {
        result = elm;
      }
      return result || this.getScrollParent(elm.parentNode);
    },
  },
  destroyed() {
    if (!this.isComplete && this.scrollParent) {
      this.scrollParent.removeEventListener('scroll', this.scrollHandler);
    }
  },
};
</script>
<style lang="scss" scoped>
.infinite-status-prompt {
  color: #666;
  font-size: 14px;
  text-align: center;
  padding: 10px 0;
}
</style>
