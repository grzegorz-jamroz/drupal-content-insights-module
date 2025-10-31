(function (Drupal) {
  'use strict';

  function calculateWordCount(text) {
    if (!text.trim()) {
      return 0;
    }
    return text.trim().split(/\s+/).length;
  }

  function calculateReadingTime(wordCount) {
    const wordsPerMinute = 200; // Average reading speed.
    const totalMinutes = wordCount / wordsPerMinute;
    const minutes = Math.floor(totalMinutes);
    const seconds = Math.round((totalMinutes - minutes) * 60);
    return {minutes, seconds};
  }

  const updateTargetWordCount = function ({wordCount, wordCountElem, targetWordCount}) {
    if (!targetWordCount || targetWordCount === 0) {
      return;
    }

    if (wordCount >= targetWordCount) {
      console.log(wordCount, targetWordCount);
      wordCountElem.style.color = 'green';
      return;
    }

    wordCountElem.style.color = 'orange';
  }

  const updateInsights = function ({text, dashboard, targetWordCount}) {
    const wordCount = calculateWordCount(text);
    const readingTime = calculateReadingTime(wordCount);

    const wordCountElem = dashboard.querySelector('[data-insight="word_count"]');
    const readingTimeElem = dashboard.querySelector('[data-insight="reading_time"]');

    if (wordCountElem) {
      wordCountElem.textContent = wordCount;
      updateTargetWordCount({wordCount, wordCountElem, targetWordCount})
    }

    if (!readingTimeElem) {
      return;
    }

    if (readingTime.minutes === 0 && readingTime.seconds < 60) {
      readingTimeElem.textContent = readingTime.seconds + ' second' + (readingTime.seconds > 9 ? 's' : '');

      return;
    }

    readingTimeElem.textContent = readingTime.minutes + ':' + (readingTime.seconds < 10 ? '0' : '') + readingTime.seconds;
  };

  Drupal.behaviors.contentInsights = {
    attach: function (context, settings) {
      const textarea = context.querySelector('textarea[data-drupal-selector="edit-body-0-value"]');
      const dashboard = context.querySelector('.content-insights-dashboard');

      if (!textarea || !dashboard) {
        return;
      }

      if (textarea.hasAttribute('data-content-insights-initialized')) {
        return;
      }

      const editorId = textarea.getAttribute('data-ckeditor5-id');

      if (!editorId) {
        return;
      }

      // it prevet race condition with CKEditor 5 initialization in create page
      function tryAttach(retryCount = 0) {
        const MAX_RETRIES = 50;

        if (retryCount >= MAX_RETRIES) {
          return;
        }

        if (typeof Drupal.CKEditor5Instances === 'undefined' || Drupal.CKEditor5Instances.size === 0) {
          setTimeout(() => tryAttach(retryCount + 1), 100);
          return;
        }

        const editor = Drupal.CKEditor5Instances.get(editorId);

        if (!editor) {
          setTimeout(() => tryAttach(retryCount + 1), 100);
          return;
        }

        textarea.setAttribute('data-content-insights-initialized', 'true');
        const targetWordCount = settings.content_insights.targetWordCount;

        editor.model.document.on('change:data', () => {
          updateInsights({text: editor.getData(), dashboard, targetWordCount});
        });
        updateInsights({text: textarea.value, dashboard, targetWordCount});
      }

      tryAttach();
    }
  };
})(Drupal);
