# DEVNOTES Polish 
hello there,

pewnie jest w moim kodzie sporo do poprawy i kilka niedociągnięć.Na pewno może się wydawać, że jest to overengineering z perspektywy zadania rekrutacyjnego i się z tym w pełni zgadzam. Zaczałem je robić wcześniej bez użycia frameworka i nie skończyłem po otrzymaniu maila od Martyny, że w sumie mogę podejść pragmatycznie do użycia frameworka i dodatkowych bibliotek.
Jakbyście chcieli zobaczyć do jakiego momentu doszedłem w tamtym podejściu to znajdziecie je w innym publicznym repo na moim githubie pod taką samą nazwą bez końcówki `_sf`.


powiem może od razu z czego nie jestem do końca zadowolony w tym podejściu
1. wchodzenie danych w Actions
   1.1 prawdopodobnie najlepiej byłoby wyjść z założenia upadaj jak najwczesniej i validować dane wejściowe z data już w DTO, ale w opisie zadania było napisane. Trochę inaczej to było opisane w readme i nie było to uwzględnione w kodzie testów, więc podszedłem do tego tak jak widać. Request mapowany na DTO ze wstępną asercią, a reszta jest deserializowana przez Deserializer i zapisywana w array data i później już sprawdzana w validatorze.
   1.2 Lepsze byłoby zastosowanie strategii w warstwie aplikacji zamiast wysyłać z Actions dwie różne commandy, ale wpadłem na to w trakcie i już nie chciałem robić refaktoru
2. notifikacje - była informacja, że klienci powinni być informowani o zdarzeniach. Nie bardzo umiałem to osadzić w kontekście jak mogliby być informawani - tutaj podparłem się, nieukrywać AI i wypluł mi jakieś broadcasty itd, ale stwierdziłem, że zrobię to po swojemu i stworzyłem wstępną abstrakcję Clienta z ustawieniami notyfikacji. Nie wiem czy dobrze, czy źle to jest. Oceńcie sami. Pewnie jeszcze jakiś webhook można by zrobić itd, ale clue jest takie, że zdecydowanie lepiej byłoby podnosić event po zapisaniu statystyk do 'bazy' i wtedy wysyłać notyfikacje.
3. obsługa exceptiontów, tutaj jestem pewny, że możnaby to zrobić znacznie lepiej, zawijać exceptiony niższego poziomu w wyższego itd.
4. Baza danych i ogólnie ustawienia środowiska - nie bawiłem się w to, bo stwierdziłem, że chyba nie ma sensu. Oczywiście nie jest problemem zrobić bazy, event storingu itd, ale wolałem się skupić na samym kodzie niż dodawać kontener z bazą, tworzyć encje encje/read modele itd. Oczywiście wszystko jest do zrobienia.
5. Pewnie przydałby się też Cache i odświeżanie go po zapisie statystyk jakimś eventem jak w przypadku notyfikacji dla klientów, ale odpuściłem temat z wyżej wymienionych powodów.
6. Brak testów w phpunit, tak powinny być. Conajmniej ja procesy biznesowe, ale powiem szczerze, że nie wiem czy zmienią one jakoś znacząco Waszą ocenę zadania. Jak coś to o testach możemy pogadać na rozmowie technicznej.
7. PHPstan i statyczna analiza kodu. Trochę zrobiłem, ale jak odpaliłem stana na 10tce to pokazał mi ponad 140 miejsc :))
8. Testy - pisałem już o jednostkowych, ale jeszcze integracyjne i akceptacyjne - chociażby żeby sprawdzić te notifikacje.

Na zadanie mi trochę zeszło, pewnie z 3-4 razy więcej niż było zakładane, ale to pewnie przez to, że poszedłem za szeroko i nie wiedziałem na co będziecie zwracać uwagę. Jak zwracacie uwagę na overengineering to tutaj pewnie poległem, ale wydaje mi się, że kod już nadawałby się do rozwoju w projekcie.

Z AI korzystałem raczej jako żółtej kaczyszki korzystałem z - grok, chat i claude. AI wygenerowało mi w sumie jedną klasę i jest to `ApiExceptionSubscriber`. Podpowiedziało mi parę razy przy debuggowaniu kodu. Myślę, że raczej nie będziecie się gniewać :)

Dzięki i czekam na feedback


# DEVNOTES English
hello there,

There's probably quite a lot in my code that could be improved and a few shortcomings. It certainly might seem like overengineering from the perspective of a recruitment task, and I fully agree with that. I started working on it earlier without using a framework and didn't finish it after receiving an email from Martyna saying that I could actually take a more pragmatic approach and use a framework and additional libraries.
If you'd like to see how far I got with that previous approach, you'll find it in another public repository on my GitHub under the same name but without the `_sf` suffix.

Let me straight away mention what I'm not entirely satisfied with in this approach:

1. Input handling in Actions  
   1.1 It would probably be best to adopt a "fail as early as possible" principle and validate input data already in the DTO, but the task description handled it a bit differently. It was described somewhat differently in the README and wasn't reflected in the test code, so I approached it as you can see. The request is mapped to a DTO with initial assertions, and the rest is deserialized by the Deserializer, stored in the `data` array, and then validated in the validator.  
   1.2 It would have been better to use a strategy pattern in the application layer instead of sending two different commands from the Actions, but I only thought of that midway through and didn't want to do a refactor.

2. Notifications – the task mentioned that clients should be informed about events. I wasn't quite sure how to place this in context (how exactly they would be informed). I won't hide it – I leaned on AI, which suggested broadcasts etc., but I decided to do it my own way and created an initial abstraction for a Client with notification settings. I don't know if it's good or bad – judge for yourselves. Of course, one could add webhooks etc., but the key point is that it would definitely be better to raise an event after saving the statistics to the "database" and then send the notifications.

3. Exception handling – I'm sure this could be done much better, e.g., wrapping lower-level exceptions in higher-level ones, etc.

4. Database and general environment setup – I didn't bother with that because I figured it probably wasn't necessary. Of course, it's not a problem to set up databases, event storage, etc., but I preferred to focus on the code itself rather than adding a database container, creating entities/read models, etc. Everything is doable, of course.

5. There would probably also be value in adding caching and invalidating it after saving statistics (via an event, similar to notifications), but I skipped that topic for the reasons mentioned above.

6. Lack of PHPUnit tests – yes, they should be there. At least for the business processes, but honestly, I'm not sure if they would significantly change your evaluation of the task. If anything, we can talk about tests during the technical interview.

7. PHPStan and static code analysis – I did a bit, but when I ran Stan at level 10 it showed over 140 issues :))

8. Tests – I already mentioned unit tests, but also integration and acceptance tests – at least to verify the notifications.

The task took me quite a while, probably 3-4 times longer than planned, most likely because I went too broad and didn't know what you would pay the most attention to. If you're focusing on overengineering, I probably failed there, but I feel the code is already in a state where it could be further developed in a real project.

I used AI mostly as a rubber duck – Grok, ChatGPT, and Claude. AI generated exactly one class for me, which is `ApiExceptionSubscriber`. It also helped me a couple of times with debugging. I think you won't be upset about that :)

Thanks and I'm looking forward to your feedback!
