Bună ziua,

Acest mesaj este autogenerat. Am verificat cuvântul zilei pe {$numDays} zile în viitor și am găsit următoarele probleme:

{foreach $messages as $message}
  * {$message.type|upper} {$message.date}: {$message.text}
{/foreach}

O zi bună,
Soacra
