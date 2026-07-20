import 'package:flutter_test/flutter_test.dart';

import 'package:mobile_sipencak/main.dart';

void main() {
  testWidgets('SIPENCAK app renders shell', (WidgetTester tester) async {
    await tester.pumpWidget(const SipencakApp());
    await tester.pump();

    expect(find.text('SIPENCAK'), findsOneWidget);
    expect(find.text('Beranda'), findsOneWidget);
    expect(find.text('Cari'), findsOneWidget);
  });
}
