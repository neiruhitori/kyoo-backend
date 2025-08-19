<!DOCTYPE html>
<html lang="en">

<head>
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0,shrink-to-fit=no, maximum-scale=1.0, user-scalable=yes">
  <meta http-equiv="Content-type" content="text/html;charset=UTF-8">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Kyoo Admin</title>
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Custom fonts for this template-->
  <link href="{{asset('admin/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="{{asset('admin/css/sb-admin-2.css')}}" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css" integrity="sha512-3JRrEUwaCkFUBLK1N8HehwQgu8e23jTH4np5NHOmQOobuC4ROQxFwFgBLTnhcnQRMs84muMh0PnnwXlPq5MGjg==" crossorigin="anonymous" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.2.3/css/flag-icons.min.css"/>
  <link rel="icon" href="{{ asset('img/favico.png') }}" type="image/icon type">

  <style>
    #container1 {
        max-width: 420px;
        min-height: 100vh;
        margin: 0 auto;
        background-color: #FFFFFF;
        position: relative;
        display: flex;
        flex-direction: column;
    }
    .rating-root {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .rating-item {
        display: flex;
        width: 2.5rem;
        height: 2rem;
        align-items: center;
        justify-content: center;
        padding: 0;
        background: transparent;
        color: #103C7C;
        border: 2px solid #103C7C;
        border-radius: 50%;
        cursor: pointer;
        font-weight: bold;
        transition: background 0.2s, color 0.2s;
    }

    .rating-item:hover {
        background: #e6eefc;
    }

    .rating-item.active {
        background: #103C7C;
        color: #fff;
    }

    .rating-item.active:hover {
        background: #103C7C; /* Tetap biru kalau aktif */
    }

    .thankyou-box {
        background-color: #E8F4FD;
        border-radius: 6px;
        color: #005AA3;
        padding: 1.2rem;
        text-align: center;
        margin-bottom: 1.125rem;
    }

    .thankyou-box h3 {
        margin-bottom: 0.2rem;
    }

  </style>
  @stack('css')
</head>

<body id="page-top" style="background-color: #ededed">

    <div id="container1">
        <div class="px-3 py-5">
            <h4 class="font-weight-bold mb-5" style="color: black">{{ $branch_name }} - Feedback</h4>
        @if ($data && $data->count())
            <form>
                @foreach($answers as $resp)
                    <div style="margin-bottom: 4rem">
                        <div class="d-flex justify-content-center mb-2">
                            <h5 class="font-weight-bold" style="color: black">
                                {{ $resp->question->question_text ?? '-' }}
                            </h5>
                        </div>
                        <div class="rating-root" data-question-id="{{ $resp->question->id }}">
                            @for ($i = 1; $i <= 10; $i++)
                                <button type="button" class="rating-item btn btn-sm
                                    {{ $resp->value == $i ? 'btn-primary' : 'btn-outline-secondary' }}"
                                    disabled>
                                    {{ $i }}
                                </button>
                            @endfor
                        </div>
                    </div>
                @endforeach
                <div class="thankyou-box">
                    <h3>Thank you for answering!</h3>
                    <h5>Your answer has been submitted</h5>
                </div>

            </form>
        @else
            @foreach ($questions as $question)
                <div style="margin-bottom: 4rem">
                    <div class="d-flex justify-content-center mb-2">
                        <h5 class="font-weight-bold" style="color: black">
                            {{ $question->question_text }}
                        </h5>
                    </div>
                    <div class="rating-root" data-question-id="{{ $question->id }}">
                        @for ($i=1;$i<=10;$i++)
                            <button class="rating-item" data-value="{{ $i }}">{{ $i }}</button>
                        @endfor
                    </div>
                </div>
            @endforeach

                <div class="d-flex justify-content-center">
                    <button type="button" id="submitSurvey" class="btn btn-primary px-5 rounded-pill">Submit</button>
                </div>
        @endif

        </div>
    </div>

  <!-- Bootstrap core JavaScript-->
  <script type="text/javascript" src="{{asset('admin/vendor/jquery/jquery.min.js')}}"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js" integrity="sha512-k6/Bkb8Fxf/c1Tkyl39yJwcOZ1P4cRrJu77p83zJjN2Z55prbFHxPs9vN7q3l3+tSMGPDdoH51AEU8Vgo1cgAA==" crossorigin="anonymous"></script>
  <script src="{{asset('admin/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

  <!-- Core plugin JavaScript-->
  <script src="{{asset('admin/vendor/jquery-easing/jquery.easing.min.js')}}"></script>

  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="{{asset('admin/js/sb-admin-2.min.js')}}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js" integrity="sha512-hUhvpC5f8cgc04OZb55j0KNGh4eh7dLxd/dPSJ5VyzqDWxsayYbojWyl5Tkcgrmb/RVKCRJI1jNlRbVP4WWC4w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script type="text/javascript" >

    let answers = {};
    const questionGroups = document.querySelectorAll(".rating-root");

    questionGroups.forEach(group => {
        let questionId = group.dataset.questionId;
        group.querySelectorAll(".rating-item").forEach(btn => {
            btn.addEventListener("click", function () {

                group.querySelectorAll(".rating-item").forEach(b => b.classList.remove("active"));
                
                this.classList.add("active");
                
                answers[questionId] = this.dataset.value;
                console.log("Jawaban sementara:", answers);
            });
        });
    });
    
     document.getElementById("submitSurvey").addEventListener("click", async function () {
        if (Object.keys(answers).length < questionGroups.length) {
            alert("Harap jawab semua pertanyaan dulu sebelum submit.");
            return;
        }

        let id = "{{ $queue_id ?? '' }}";
        let data = {
            rating: answers,
            is_liked: false,
            bookingId: id,
        };

        try {
            await axios.post(`/api/direct-queue/${id}/feedback`, data);
            document.querySelectorAll("button").forEach(btn => btn.disabled = true);

            document.querySelector("#container1").insertAdjacentHTML("beforeend", `
                <div class="thankyou-box">
                    <h3>Thank you for answering!</h3>
                    <h5>Your answer has been submitted</h5>
                </div>
            `);
        } catch (error) {
            console.error(error);
            alert("Gagal mengirim jawaban. Silakan coba lagi.");
        }
    });

    </script>
  @stack('js')
