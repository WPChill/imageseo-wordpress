import { createGlobalStyle } from "styled-components";

const GlobalStyle = createGlobalStyle`
    :root {
        --white: #fff;
        --grey-light: #fafafc;
        --grey-base: #f5f5fa;
        --grey-dark: #dadae0;
        --grey-shady: #a0a0a3;
        --black-light: #0e214d;
        --black-base: #08142e;
        --black-dark: #00081a;
        --red-light: #fab1a0;
        --red-base: #fa7455;
        --red-dark: #402d29;
        --blue-light: #abe0ff;
        --blue-degraded: #179ae6;
        --blue-base: #179ae5;
        --blue-dark: #0a4566;
        --purple-light: #bbbdf2;
        --purple-base: #3139cc;
        --purple-dark: #151959;
        --yellow-light: #ffeaa7;
        --yellow-base: #ffd140;
        --yellow-dark: #4d3f13;
        --blizzard-light: #99ece5;
        --blizzard-base: #04edda;
        --blizzard-dark: #014d46;
        --red-error: #e64c2e;
    }

    .mt-1 {
        margin-top: 5px;
    }
    .mt-2 {
        margin-top: 10px;
    }
    .mt-3 {
        margin-top: 15px;
    }
    .mt-4 {
        margin-top: 30px;
    }
    .mt-5 {
        margin-top: 45px;
    }
    .mt-6 {
        margin-top: 60px;
    }
    .mt-7 {
        margin-top: 90px;
    }

    .ml-1 {
        margin-left: 5px;
    }
    .ml-2 {
        margin-left: 10px;
    }
    .ml-3 {
        margin-left: 15px;
    }
    .ml-4 {
        margin-left: 30px;
    }
    .ml-5 {
        margin-left: 45px;
    }
    .ml-6 {
        margin-left: 60px;
    }
    .ml-7 {
        margin-left: 90px;
    }

    .mr-1 {
        margin-right: 5px;
    }
    .mr-2 {
        margin-right: 10px;
    }
    .mr-3 {
        margin-right: 15px;
    }
    .mr-4 {
        margin-right: 30px;
    }
    .mr-5 {
        margin-right: 45px;
    }
    .mr-6 {
        margin-right: 60px;
    }
    .mr-7 {
        margin-right: 90px;
    }

    .mb-1 {
        margin-bottom: 5px;
    }
    .mb-2 {
        margin-bottom: 10px;
    }
    .mb-3 {
        margin-bottom: 15px;
    }
    .mb-4 {
        margin-bottom: 30px;
    }
    .mb-5 {
        margin-bottom: 45px;
    }
    .mb-6 {
        margin-bottom: 60px;
    }
    .mb-7 {
        margin-bottom: 90px;
    }

    .text-center {
        text-align: center;
    }

    .d-flex {
        display:flex;
    }

    .justify-content-center{
        justify-content:center;
    }
    .align-items-center{
        align-items:center;
    }
`;

export default GlobalStyle;
