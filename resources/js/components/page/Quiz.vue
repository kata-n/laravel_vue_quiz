<template>
  <div>
    <main>
      <div class="container">
        <article class="col-md-8 col-xs-12">
          <section>
            <h2 class="quiz-question-h2">
              <img class="quiz-question__logo" src="/images/what-is-mark.png" />
              第{{ quizNumber }}問
            </h2>
            <p>{{ title }}</p>
            <div class="quiz-answer__list">
              <ul>
                <li v-for="(answer, index) in answers" :key="index">
                  <a>
                    <button
                      @click="goAnswer(index + 1)"
                      :disabled="isAlreadyAnswered"
                    >{{ index + 1 }}</button>
                  </a>
                  {{ answer }}
                </li>
              </ul>
            </div>
            <div class="text-right">カテゴリー:{{ categoryName }}</div>
          </section>
          <section>
            <h2 class="quiz-correct-h2">
              <img class="quiz-correct__logo" src="/images/correct-mark.png" />正解
            </h2>
            <p>
              <button
                class="quiz-correct-answer"
                v-show="isAlreadyAnswered"
                disabled
              >{{ correctAnswerNo }}</button>
            </p>
            <button @click="goAnswer(0)" v-show="!isAlreadyAnswered">正解を表示する</button>
            <div class="alert alert-info" v-show="isCorrect">
              <strong>正解!</strong>
            </div>
            <div class="alert alert-danger" v-show="isMistake">
              <strong>不正解!</strong>
            </div>
          </section>
          <section>
            <h2 class="quiz-commentary-h2">
              <img class="quiz-commentary__logo" src="/images/commentary-mark.png" />解説
            </h2>
            <div
              class="quiz-commentary__text"
              v-show="isAlreadyAnswered"
              style="white-space:pre-wrap; word-wrap:break-word;"
            >{{ commentary }}</div>
            <button
              type="button"
              class="btn btn-primary center-block"
              @click="goNextQuiz"
              v-if="!isQuizFinish"
            >次の問題へ</button>
            <button
              type="button"
              class="center-block"
              data-toggle="modal"
              data-target="#modal-result"
              v-if="isQuizFinish"
              @click="showResult"
            >結果を表示する</button>
          </section>
        </article>
        <the-sidebar></the-sidebar>
      </div>
    </main>
    <the-modal :correctPercentageObject="correctPercentageObject" ref="modal"></the-modal>
  </div>
</template>

<script>
import TheSidebar from "../layout/TheSidebar";
import TheModal from "../module/TheModal";

export default {
  components: {
    TheSidebar,
    TheModal
  },

  data() {
    return {
      quizData: [],

      title: "",
      imageSrc: "",
      answers: [],
      commentary: "",
      correctAnswerNo: 0,
      isCorrect: false,
      isMistake: false,
      isAlreadyAnswered: false,
      isQuizFinish: false,
      score: 0,
      quizNumber: 1,
      categoryName: "",
      correctPercentageObject: {}
    };
  },
  mounted() {
    const categories = this.$route.query.categories;
    this.$http.get(`/api/quiz?categories=${categories}`).then(response => {
      this.quizData = response.data;
      this.findNextQuiz(0);
      console.log(this.quizData);
    });
  },
  methods: {
    goAnswer(selectAnswerNum) {
      if (selectAnswerNum === 0) {
        this.isCorrect = false;
        this.isMistake = false;
      } else if (selectAnswerNum === Number(this.correctAnswerNo)) {
        //正解の場合
        this.isCorrect = true;
        this.isMistake = false;
        this.score += 1;
      } else {
        //不正解の場合
        this.isMistake = true;
        this.isCorrect = false;
      }
      this.isAlreadyAnswered = true;

      //10回以上回答している場合はクイズを終了
      if (this.quizNumber >= 10) {
        this.endQuiz();
      }
    },
    findNextQuiz(quizNumber) {
      this.title = this.quizData[quizNumber].title;
      this.answers = [
        this.quizData[quizNumber].answer.answer_1,
        this.quizData[quizNumber].answer.answer_2,
        this.quizData[quizNumber].answer.answer_3,
        this.quizData[quizNumber].answer.answer_4
      ];
      this.commentary = this.quizData[quizNumber].answer.commentary;
      this.correctAnswerNo = this.quizData[quizNumber].answer.correct_answer_no;
      this.categoryName = this.quizData[quizNumber].category.name;
    },
    goNextQuiz() {
      //次の問題へをクリック
      if (this.quizNumber >= 10) {
        //10問終わったらクイズを終了する
        this.endQuiz();
      } else {
        // 次のクイズを表示し、クイズ番号を加算、alert-info、alert-danger、解説を非表示にする
        this.findNextQuiz(this.quizNumber);
        this.quizNumber += 1;
        this.isCorrect = false;
        this.isMistake = false;
        this.isAlreadyAnswered = false;
      }
    },
    endQuiz() {
      this.isQuizFinish = true;
      this.answerNo = "-";
      this.isAlreadyAnswered = true;
      this.correctPercentageObject = {
        correctScore: this.score,
        mistakeScore: 10 - this.score
      };
    },
    showResult() {
      this.$refs.modal.render();
    }
  }
};
</script>
